<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppyfuriousAdminUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $this->createResourcesSymlink($container);
    }

    private function createResourcesSymlink(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');
        $origRealDir = __DIR__ . '/Resources/AdminUserBundle';
        $originDir = str_replace($projectDir, '../..', $origRealDir);
        $fs = new Filesystem();
        //  symfony >= 3.4
        if (file_exists($projectDir . '/templates')) {
            $translationProjectDir = $projectDir . '/translations';
            $translationOriginDir = str_replace($projectDir, '..', $origRealDir) . '/translations';
            $translationOriginRealDir = $origRealDir . '/translations';
            foreach (array_diff(scandir($translationOriginRealDir), ['..', '.']) as $translationFileName) {
                $fs->symlink(
                    $translationOriginDir . '/' . $translationFileName,
                    $translationProjectDir . '/' . $translationFileName
                );
            }
        } else {
            $targetDir = $projectDir . '/app/Resources/AdminUserBundle';
            if (!$fs->exists($targetDir)) {
                $fs->symlink($originDir, $targetDir);
            }
        }
    }

}