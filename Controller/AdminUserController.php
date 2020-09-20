<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Controller;

use Appyfurious\AdminUserBundle\DTO\UserOptionsDto;
use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Appyfurious\AdminUserBundle\Form\ConfirmUserType;
use Appyfurious\AdminUserBundle\Form\CreateUserType;
use Appyfurious\AdminUserBundle\Form\EditUserType;
use Appyfurious\AdminUserBundle\Repository\AdminUserRepository;
use Appyfurious\AdminUserBundle\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(EntityManagerInterface $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="appyfurious_admin_homepage")
     * @return Response
     */
    public function indexAction(): Response
    {
        return new Response('Hello, World! Please override route "appyfurious_admin_homepage" for set homepage');
    }

    /**
     * @Route("/admin_user/list", name="appyfurious_admin_user_list")
     * @return Response
     */
    public function listAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN_USER_LIST');

        $adminUserRepo = $this->em->getRepository(AdminUser::class);
        $users = $adminUserRepo->findUserByActiveStatus();

        return $this->render('@AppyfuriousAdminUser/AdminUser/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin_user/create", name="appyfurious_admin_user_create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN_USER_CREATE');

        $user = new AdminUser();

        $form = $this->createForm(CreateUserType::class);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken($request->get('create')['_token']);
            $user->setRoles([$request->get('button_role')]);
            $this->em->persist($user);
            $this->em->flush();
            $userOptions = new UserOptionsDto($user->getUsername(), $user->getEmail(), $this->generateUrl('appyfurious_admin_user_confirm', ['token' => $request->get('create')['_token']
            ], UrlGeneratorInterface::ABSOLUTE_URL));

            $this->mailer->sendWelcomeEmail($request->getHost(), $userOptions);
            return new RedirectResponse($this->generateUrl('appyfurious_admin_user_list'));
        }

        return $this->render('@AppyfuriousAdminUser/AdminUser/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin_user/edit/{userId}", name="appyfurious_admin_user_edit")
     * @param Request $request
     * @param string $userId
     * @return Response
     */
    public function editAction(string $userId, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN_USER_EDIT');

        $adminUserRepo = $this->em->getRepository(AdminUser::class);
        $user = $adminUserRepo->find($userId);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('AdminUser with id "%s" not found', $userId));
        }

        $form = $this->createForm(EditUserType::class);

        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->getData()->getUsername());
            $user->setEmail($form->getData()->getEmail());
            $user->setEnabled($form->getData()->isEnabled());
            $user->setRoles($request->get('edit')['roles']);

            $this->em->persist($user);
            $this->em->flush();

            return new RedirectResponse($this->generateUrl('appyfurious_admin_user_list'));
        }

        return $this->render('@AppyfuriousAdminUser/AdminUser/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/confirm/{token}", name="appyfurious_admin_user_confirm")
     * @param string $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function confirmAction($token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $adminUserRepo = $this->em->getRepository(AdminUser::class);
        $user = $adminUserRepo->findUserByToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $form = $this->createForm(ConfirmUserType::class);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setEnabled(true);
            $user->setActive(true);
            $encodedPass = $encoder->encodePassword($user, $request->get('user_change_password')['password']['first']);
            $user->setPassword($encodedPass);
            $this->em->persist($user);
            $this->em->flush();
            return new RedirectResponse($this->generateUrl('appyfurious_admin_user_list'));
        }

        return $this->render('@AppyfuriousAdminUser/AdminUser/confirm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
