<?php

namespace Appyfurious\AdminUserBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'appyfurious_admin_login';

    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('data'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        // Load / create our user however you need.
        // You can do this by calling the user provider, or with custom logic here.
        $user = $userProvider->loadUserByUsername($credentials['username']);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('username could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
       return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param array $credentials
     * @return string|null
     */
    public function getPassword(array $credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('appyfurious_admin_homepage'));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
