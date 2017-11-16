<?php

namespace StarterKit\StartBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StarterKitStartBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // This is done so we can test the mapped supper class user
        if ($container->getParameter('kernel.environment') === 'test') {
            // With annotation configuration format
            $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                ['StarterKit\StartBundle\Tests\Entity'], [__DIR__ . '/../../StarterKit/StartBundle/Tests/Entity']
            ));
        }
    }


}
