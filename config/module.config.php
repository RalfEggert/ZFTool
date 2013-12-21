<?php
return array(
    'ZFTool' => array(
        'disable_usage' => false,    // set to true to disable showing available ZFTool commands in Console.
    ),

    // -----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=

    'controllers' => array(
        'factories' => array(
            'ZFTool\Controller\Create'      => 'ZFTool\Controller\CreateControllerFactory',
        ),
        'invokables' => array(
            'ZFTool\Controller\Info'        => 'ZFTool\Controller\InfoController',
            'ZFTool\Controller\Config'      => 'ZFTool\Controller\ConfigController',
            'ZFTool\Controller\Module'      => 'ZFTool\Controller\ModuleController',
            'ZFTool\Controller\Classmap'    => 'ZFTool\Controller\ClassmapController',
            'ZFTool\Controller\Install'     => 'ZFTool\Controller\InstallController',
            'ZFTool\Controller\Diagnostics' => 'ZFTool\Controller\DiagnosticsController',
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            'ZFTool\Generator\ModuleGenerator' => 'ZFTool\Generator\ModuleGenerator',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'zf-tool/diagnostics/run' => __DIR__ . '/../view/diagnostics/run.phtml',
        )
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'zftool-version' => array(
                    'options' => array(
                        'route'    => 'version',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Info',
                            'action'     => 'version',
                        ),
                    ),
                ),
                'zftool-version2' => array(
                    'options' => array(
                        'route'    => '--version',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Info',
                            'action'     => 'version',
                        ),
                    ),
                ),
                'zftool-config-list' => array(
                    'options' => array(
                        'route'    => 'config list [--local|-l]:local',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Config',
                            'action'     => 'list',
                        ),
                    ),
                ),
                'zftool-config' => array(
                    'options' => array(
                        'route'    => 'config <action> [<arg1>] [<arg2>]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Config',
                            'action'     => 'get',
                        ),
                    ),
                ),
                'zftool-classmap-generate' => array(
                    'options' => array(
                        'route'    => 'classmap generate <directory> [<destination>] [--append|-a] [--overwrite|-w]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Classmap',
                            'action'     => 'generate',
                        ),
                    ),
                ),
                'zftool-modules-list' => array(
                    'options' => array(
                        'route'    => 'modules [list]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Module',
                            'action'     => 'list',
                        ),
                    ),
                ),
                'zftool-create-project' => array(
                    'options' => array(
                        'route'    => 'create project <path>',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Create',
                            'action'     => 'project',
                        ),
                    ),
                ),
                'zftool-create-module' => array(
                    'options' => array(
                        'route'    => 'create module <moduleName> [<path>] [--ignore-conventions|-i] [--no-docblocks|-d]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Create',
                            'action'     => 'module',
                        ),
                    ),
                ),
                'zftool-create-controller' => array(
                    'options' => array(
                        'route'    => 'create controller <controllerName> <moduleName> [<path>] [--ignore-conventions|-i] [--no-config|-n] [--no-docblocks|-d]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Create',
                            'action'     => 'controller',
                        ),
                    ),
                ),
                'zftool-create-action' => array(
                    'options' => array(
                        'route'    => 'create action <actionName> <controllerName> <moduleName> [<path>] [--ignore-conventions|-i] [--no-docblocks|-d]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Create',
                            'action'     => 'method',
                        ),
                    ),
                ),
                'zftool-create-routing' => array(
                    'options' => array(
                        'route'    => 'create routing <moduleName> [<path>] [--single-route|-s]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Create',
                            'action'     => 'routing',
                        ),
                    ),
                ),
                'zftool-install-zf' => array(
                    'options' => array(
                        'route'    => 'install zf <path> [<version>]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Install',
                            'action'     => 'zf',
                        ),
                    ),
                ),
                'zftool-diagnostics' => array(
                    'options' => array(
                        'route'    => '(diagnostics|diag) [-v|--verbose]:verbose [--debug] [-q|--quiet]:quiet [-b|--break]:break [<testGroupName>]',
                        'defaults' => array(
                            'controller' => 'ZFTool\Controller\Diagnostics',
                            'action'     => 'run',
                        ),
                    ),
                ),
            ),
        ),
    ),

    'diagnostics' => array(
        'ZF' => array(
            'PHP Version' => array('ZFTool\Diagnostics\Test\PhpVersion', '5.3.3'),
        )
    )
);
