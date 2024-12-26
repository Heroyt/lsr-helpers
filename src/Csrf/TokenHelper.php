<?php
declare(strict_types=1);

namespace Lsr\Helpers\Csrf;

use Lsr\Interfaces\SessionInterface;
use Random\Randomizer;
use RuntimeException;

class TokenHelper
{

    private static TokenHelper $instance;

    public function __construct(
        private readonly SessionInterface $session
    ) {}

    public static function getInstance(?SessionInterface $session) : TokenHelper {
        if (!isset(self::$instance)) {
            if ($session === null) {
                throw new RuntimeException('Cannot instantiate session object');
            }
            self::$instance = new self($session);
        }
        return self::$instance;
    }

    public function formToken(string $prefix = ''): string {
        $sessionKey = $prefix.'_csrf_hash';
        if (empty($this->session->get($sessionKey))) {
            $this->session->set(
                $sessionKey,
                bin2hex(
                    (new Randomizer)->getBytes(32)
                )
            );
        }
        $hash = $this->session->get($sessionKey, '');
        assert(is_string($hash));
        return $hash;
    }

    public function isTokenValid(string $token, string $check = '') : bool {
        if (empty($check)) {
            $check = $this->session->get('_csrf_hash', '');
            assert(is_string($check));
        }
        return hash_equals($check, $token);
    }

    public function formValid(string $name, ?string $token = '') : bool {
        $hash = $this->session->get($name.'_csrf_hash', '');
        assert(is_string($hash));
        $hash = hash_hmac('sha256', $name, $hash);
        return isTokenValid($token ?? '', $hash);
    }

}