<?php

return [
    'ServicesBundle' => new \Symfony\Component\DependencyInjection\Kernel\ServicesBundle(),
    'ConsoleBundle' => new \Symfony\Component\Console\ConsoleBundle(),
    'FrameworkBundle' => new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    'DoctrineBundle' => new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    'DoctrineMigrationsBundle' => new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
    'DebugBundle' => new \Symfony\Bundle\DebugBundle\DebugBundle(),
    'TwigBundle' => new \Symfony\Bundle\TwigBundle\TwigBundle(),
    'WebProfilerBundle' => new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
    'StimulusBundle' => new \Symfony\UX\StimulusBundle\StimulusBundle(),
    'TurboBundle' => new \Symfony\UX\Turbo\TurboBundle(),
    'TwigExtraBundle' => new \Twig\Extra\TwigExtraBundle\TwigExtraBundle(),
    'SecurityBundle' => new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
    'MonologBundle' => new \Symfony\Bundle\MonologBundle\MonologBundle(),
    'MakerBundle' => new \Symfony\Bundle\MakerBundle\MakerBundle(),
];
