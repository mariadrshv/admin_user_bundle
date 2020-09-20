<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Controller;

use Appyfurious\AdminUserBundle\DTO\UserOptionsDto;
use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Appyfurious\AdminUserBundle\Form\ResettingPassType;
use Appyfurious\AdminUserBundle\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResettingController extends AbstractController
{
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(EntityManagerInterface $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/resetting", name="appyfurious_admin_resetting")
     * @return Response
     */
    public function resettingAction(): Response
    {
        return $this->render('@AppyfuriousAdminUser/Resetting/resetting.html.twig');
    }

    /**
     * Request reset user password: submit form and send email.
     * @param Request $request
     * @return RedirectResponse
     * @Route("/send-email", name="appyfurious_admin_resetting_send_email")
     */
    public function sendEmailAction(Request $request): RedirectResponse
    {
        $username = $request->request->get('username');
        $adminUserRepo = $this->em->getRepository(AdminUser::class);
        $user = $adminUserRepo->findUserByUsernameOrEmail($username);
        $this->setTokenToUser($user);
        $userOptions = new UserOptionsDto($user->getUsername(), $user->getEmail(), $this->generateUrl('appyfurious_admin_reset_pass',
            ['token' => $user->getConfirmationToken()
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        $this->mailer->sendResetEmail($userOptions);

        return new RedirectResponse($this->generateUrl('appyfurious_admin_resetting_check_email', array('username' => $username)));
    }

    private function setTokenToUser(AdminUser $user): void
    {
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $user->setConfirmationToken($token);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Request $request
     * @Route("/check-email", name="appyfurious_admin_resetting_check_email")
     * @return RedirectResponse|Response
     */
    public function checkEmailAction(Request $request): Response
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('appyfurious_admin_resetting'));
        }

        return $this->render('@AppyfuriousAdminUser/Resetting/check_email.html.twig');
    }

    /**
     * @Route("/resetting/reset/{token}", name="appyfurious_admin_reset_pass")
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function resetAction(Request $request, $token, UserPasswordEncoderInterface $encoder): Response
    {
        $adminUserRepo = $this->em->getRepository(AdminUser::class);
        $user = $adminUserRepo->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse($this->generateUrl('appyfurious_admin_login'));
        }

        $form = $this->createForm(ResettingPassType::class);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPass = $encoder->encodePassword($user, $form->getData()->getPassword());
            $user->setPassword($encodedPass);
            $user->setConfirmationToken('');
            $this->em->persist($user);
            $this->em->flush();

            return new RedirectResponse($this->generateUrl('appyfurious_admin_login'));
        }

        return $this->render('@AppyfuriousAdminUser/Resetting/reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }
}