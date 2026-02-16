<?php

namespace Tests\Feature\Security;

use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class PublicAssetsTest extends TestCase
{
    public function test_every_theme_file_the_views_link_to_exists(): void
    {
        $missing = [];

        foreach (Finder::create()->files()->in(resource_path('views'))->name('*.blade.php') as $view) {
            preg_match_all("#(?:versioned_)?asset\\(\\s*[\"']((?:AdminLTE|cozastore)/[^\"']+)[\"']#", $view->getContents(), $matches);

            foreach ($matches[1] as $path) {
                if (! is_file(public_path($path))) {
                    $missing[] = $view->getRelativePathname() . ' -> ' . $path;
                }
            }
        }

        $this->assertSame([], $missing, "Views link to theme files that do not exist:\n" . implode("\n", $missing));
    }

    public function test_theme_demo_pages_and_docs_are_not_published(): void
    {
        $this->assertSame([], glob(public_path('cozastore/*.html')));
        $this->assertSame([], glob(public_path('AdminLTE/*.html')));
        $this->assertDirectoryDoesNotExist(public_path('AdminLTE/docs'));
        $this->assertDirectoryDoesNotExist(public_path('AdminLTE/pages'));
        $this->assertFileDoesNotExist(public_path('AdminLTE/composer.json'));
    }
}
