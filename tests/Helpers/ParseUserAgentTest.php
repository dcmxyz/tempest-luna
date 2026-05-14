<?php

declare(strict_types=1);

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function App\Helpers\parse_user_agent;

/**
 * @internal
 */
final class ParseUserAgentTest extends TestCase
{
    #[Test]
    public function detects_chrome_on_windows(): void
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        $this->assertSame('Chrome - Desktop (Windows)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_firefox_on_linux(): void
    {
        $ua = 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0';

        $this->assertSame('Firefox - Desktop (Linux)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_safari_on_macos(): void
    {
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';

        $this->assertSame('Safari - Desktop (macOS)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_chrome_mobile_on_android(): void
    {
        $ua = 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36';

        $this->assertSame('Chrome - Mobile (Android)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_safari_on_iphone(): void
    {
        $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

        $this->assertSame('Safari - Mobile (iOS)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_safari_on_ipad(): void
    {
        $ua = 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/604.1';

        $this->assertSame('Safari - Tablet (iPadOS)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_edge_on_windows(): void
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0';

        $this->assertSame('Edge - Desktop (Windows)', parse_user_agent($ua));
    }

    #[Test]
    public function detects_opera_on_windows(): void
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OPR/106.0.0.0';

        $this->assertSame('Opera - Desktop (Windows)', parse_user_agent($ua));
    }

    #[Test]
    public function returns_unknown_browser_and_os_for_empty_string(): void
    {
        $result = parse_user_agent('');

        $this->assertStringContainsString('Unknown Browser', $result);
        $this->assertStringContainsString('Unknown OS', $result);
        $this->assertStringContainsString('Desktop', $result);
    }

    #[Test]
    public function detects_samsung_browser(): void
    {
        $ua = 'Mozilla/5.0 (Linux; Android 13; SM-G998B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.36';

        $this->assertSame('Samsung Browser - Mobile (Android)', parse_user_agent($ua));
    }

    /**
     * @param non-empty-string $userAgent
     */
    #[Test]
    #[DataProvider('desktopUserAgents')]
    public function detects_desktop_device_type(string $userAgent): void
    {
        $result = parse_user_agent($userAgent);

        $this->assertStringContainsString('Desktop', $result);
    }

    /**
     * @return array<string, array{0: non-empty-string}>
     */
    public static function desktopUserAgents(): array
    {
        return [
            'chrome windows' => [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
            ],
            'firefox linux' => ['Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0'],
            'safari macos' => [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 Version/17.0 Safari/605.1.15',
            ],
        ];
    }
}
