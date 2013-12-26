<?php
namespace ZFTool\Generator;

use Zend\Code\Generator\AbstractGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\Exception\RuntimeException as GeneratorException;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ValueGenerator;
use Zend\Code\Reflection\FileReflection;
use ZFTool\Options\RequestOptions;

/**
 * Class ModuleGenerator
 *
 * @package ZFTool\Generator
 */
class ModuleGenerator
{
    /**
     * @var boolean
     */
    protected $flagCreateDocBlocks = true;
    /**
     * @var RequestOptions
     */
    protected $requestOptions;

    /**
     * @param $requestOptions
     */
    function __construct(RequestOptions $requestOptions)
    {
        $this->requestOptions = $requestOptions;

        // change doc block flag
        $this->flagCreateDocBlocks = !$this->requestOptions->getFlagNoDocBlocks();
    }

    /**
     * Create module configuration
     *
     * @param array $configData
     *
     * @return bool
     */
    public function createConfiguration(array $configData = array())
    {
        // get needed options to shorten code
        $modulePath = $this->requestOptions->getModulePath();
        $configFile = $modulePath . '/config/module.config.php';

        // create config array
        $array = new ValueGenerator();
        $array->initEnvironmentConstants();
        $array->setValue($configData);
        $array->setArrayDepth(0);

        // create file with file generator
        $file = new FileGenerator();
        $file->setBody(
            'return ' . $array->generate() . ';' . AbstractGenerator::LINE_FEED
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $file->setDocBlock(
                new DocBlockGenerator(
                    'Configuration file generated by ZFTool',
                    null,
                    array(
                        $this->generateSeeTag(),
                    )
                )
            );
        }

        // write application configuration
        if (!file_put_contents($configFile, $file->generate())) {
            return false;
        }

        return true;
    }

    /**
     * Create controller class
     *
     * @return bool
     */
    public function createController()
    {
        // get needed options to shorten code
        $moduleName         = $this->requestOptions->getModuleName();
        $controllerClass    = $this->requestOptions->getControllerClass();
        $controllerPath     = $this->requestOptions->getControllerPath();
        $controllerFile     = $this->requestOptions->getControllerFile();
        $controllerFilePath = $controllerPath . $controllerFile;

        // create controller class with class generator
        $code = new ClassGenerator();
        $code->setNamespaceName($moduleName . '\Controller');
        $code->addUse('Zend\Mvc\Controller\AbstractActionController');
        $code->addUse('Zend\View\Model\ViewModel');
        $code->setName($controllerClass);
        $code->setExtendedClass('AbstractActionController');
        $code->addMethodFromGenerator(
            $this->generateActionMethod('indexAction')
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $code->setDocBlock(
                new DocBlockGenerator(
                    'Class ' . $controllerClass,
                    'Please add a proper description for the '
                    . $controllerClass,
                    array(
                        $this->generatePackageTag($moduleName),
                    )
                )
            );
        }

        // create file with file generator
        $file = new FileGenerator();
        $file->setClass($code);

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $file->setDocBlock(
                new DocBlockGenerator(
                    'This file was generated by ZFTool.',
                    null,
                    array(
                        $this->generatePackageTag($moduleName),
                        $this->generateSeeTag(),
                    )
                )
            );
        }

        // write controller class
        if (!file_put_contents($controllerFilePath, $file->generate())) {
            return false;
        }

        return true;
    }

    /**
     * Create the Module.php content
     *
     * @return string
     */
    public function createModule()
    {
        // get needed options to shorten code
        $moduleName    = $this->requestOptions->getModuleName();
        $modulePath    = $this->requestOptions->getModulePath();
        $moduleFile    = $modulePath . '/Module.php';
        $moduleViewDir = $this->requestOptions->getModuleViewDir();

        // create dirs
        mkdir($modulePath . '/config', 0777, true);
        mkdir($modulePath . '/src/' . $moduleName . '/Controller', 0777, true);
        mkdir($modulePath . '/view/' . $moduleViewDir, 0777, true);

        // create controller class with class generator
        $code = new ClassGenerator();
        $code->setNamespaceName($moduleName);
        $code->setName('Module');
        $code->addMethodFromGenerator($this->generateGetConfigMethod());
        $code->addMethodFromGenerator(
            $this->generateGetAutoloaderConfigMethod()
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $code->setDocBlock(
                new DocBlockGenerator(
                    'Module',
                    'Please add a proper description for the '
                    . $moduleName . ' module',
                    array(
                        $this->generatePackageTag($moduleName),
                    )
                )
            );
        }

        // create file with file generator
        $file = new FileGenerator();
        $file->setClass($code);

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $file->setDocBlock(
                new DocBlockGenerator(
                    'This file was generated by ZFTool.',
                    null,
                    array(
                        $this->generatePackageTag($moduleName),
                        $this->generateSeeTag(),
                    )
                )
            );
        }

        // write controller class
        if (!file_put_contents($moduleFile, $file->generate())) {
            return false;
        }

        return true;
    }

    /**
     * Create view script
     *
     * @return bool
     */
    public function createViewScript()
    {
        // get needed options to shorten code
        $moduleName         = $this->requestOptions->getModuleName();
        $controllerName     = $this->requestOptions->getControllerName();
        $actionName         = $this->requestOptions->getActionName();
        $actionViewPath     = $this->requestOptions->getActionViewPath();
        $controllerViewPath = $this->requestOptions->getControllerViewPath();

        // create dir if not exists
        if (!file_exists($controllerViewPath)) {
            mkdir($controllerViewPath, 0777, true);
        }

        // setup view script body
        $viewBody   = array();
        $viewBody[] = '?>';
        $viewBody[] = '<div class="jumbotron">';
        $viewBody[] = '<h1>Action "' . $actionName . '"</h1>';
        $viewBody[] = '<p>Created for Controller "' . $controllerName
            . '" in Module "' . $moduleName . '"</p>';
        $viewBody[] = '</div>';

        // create file with file generator
        $file = new FileGenerator();
        $file->setBody(
            implode(AbstractGenerator::LINE_FEED, $viewBody)
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $file->setDocBlock(
                new DocBlockGenerator(
                    'View script generated by ZFTool',
                    null,
                    array(
                        $this->generatePackageTag($moduleName),
                    )
                )
            );
        }

        // write view script
        if (!file_put_contents($actionViewPath, $file->generate())) {
            return false;
        }

        return true;
    }

    /**
     * @param array $configData
     * @param       $configFile
     *
     * @return bool
     */
    public function updateConfiguration(array $configData, $configFile)
    {
        // set old file
        $oldFile = str_replace('.php', '.old', $configFile);

        // copy to old file
        copy($configFile, $oldFile);

        // create config array
        $array = new ValueGenerator();
        $array->initEnvironmentConstants();
        $array->setValue($configData);
        $array->setArrayDepth(0);

        // create file with file generator
        $file = new FileGenerator();
        $file->setBody(
            'return ' . $array->generate() . ';' . AbstractGenerator::LINE_FEED
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $file->setDocBlock(
                new DocBlockGenerator(
                    'Configuration file generated by ZFTool',
                    'The previous configuration file is stored in ' . $oldFile,
                    array(
                        $this->generateSeeTag(),
                    )
                )
            );
        }

        // write application configuration
        if (!file_put_contents($configFile, $file->generate())) {
            return false;
        }

        return true;
    }

    /**
     * Update controller class with new action
     *
     * @return bool
     * @throws \Zend\Code\Generator\Exception
     */
    public function updateController()
    {
        // get needed options to shorten code
        $moduleName         = $this->requestOptions->getModuleName();
        $controllerKey      = $this->requestOptions->getControllerKey();
        $controllerPath     = $this->requestOptions->getControllerPath();
        $controllerFile     = $this->requestOptions->getControllerFile();
        $actionMethod       = $this->requestOptions->getActionMethod();
        $controllerFilePath = $controllerPath . $controllerFile;

        // get file and class reflection
        $fileReflection  = new FileReflection(
            $controllerFilePath,
            true
        );
        $classReflection = $fileReflection->getClass(
            $controllerKey . 'Controller'
        );

        // setup class generator with reflected class
        $code = ClassGenerator::fromReflection($classReflection);

        // check for action method
        if ($code->hasMethod($actionMethod)) {
            throw new GeneratorException(
                'New action already exists within controller'
            );
        }

        // fix namespace usage
        $code->addUse('Zend\Mvc\Controller\AbstractActionController');
        $code->addUse('Zend\View\Model\ViewModel');
        $code->setExtendedClass('AbstractActionController');
        $code->addMethodFromGenerator(
            $this->generateActionMethod($actionMethod)
        );

        // create file with file generator
        $file = new FileGenerator();
        $file->setClass($code);

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $file->setDocBlock(
                new DocBlockGenerator(
                    'Configuration file was generated by ZFTool.',
                    null,
                    array(
                        $this->generatePackageTag($moduleName),
                        $this->generateSeeTag(),
                    )
                )
            );
        }

        // write controller class
        if (!file_put_contents($controllerFilePath, $file->generate())) {
            return false;
        }

        return true;
    }

    /**
     * Generate the action method
     *
     * @return MethodGenerator
     */
    protected function generateActionMethod($methodName)
    {
        // create method
        $method = new MethodGenerator();
        $method->setName($methodName);
        $method->setBody(
            'return new ViewModel();'
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $method->setDocBlock(
                new DocBlockGenerator(
                    'Method ' . $methodName,
                    'Please add a proper description for this action',
                    array(
                        $this->generateReturnTag('ViewModel'),
                    )
                )
            );
        }

        return $method;
    }

    /**
     * Generate the getAutoloaderConfig() method
     *
     * @return MethodGenerator
     */
    protected function generateGetAutoloaderConfigMethod()
    {
        // create method body
        $body = new ValueGenerator;
        $body->initEnvironmentConstants();
        $body->setValue(
            array(
                'Zend\Loader\StandardAutoloader' => array(
                    'namespaces' => array(
                        '__NAMESPACE__ => __DIR__ . \'/src/\' . __NAMESPACE__',
                    ),
                ),
            )
        );

        // create method
        $method = new MethodGenerator();
        $method->setName('getAutoloaderConfig');
        $method->setBody(
            'return ' . $body->generate() . ';'
            . AbstractGenerator::LINE_FEED
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $method->setDocBlock(
                new DocBlockGenerator(
                    'Get autoloader configuration',
                    null,
                    array(
                        $this->generateReturnTag('array'),
                    )
                )
            );
        }

        return $method;
    }

    /**
     * Generate the getConfig() method
     *
     * @return MethodGenerator
     */
    protected function generateGetConfigMethod()
    {
        // create method body
        $body = new ValueGenerator;
        $body->initEnvironmentConstants();
        $body->setValue(
            'include __DIR__ . \'/config/module.config.php\''
        );

        // create method
        $method = new MethodGenerator();
        $method->setName('getConfig');
        $method->setBody(
            'return ' . $body->generate() . ';'
            . AbstractGenerator::LINE_FEED
        );

        // add optional doc block
        if ($this->flagCreateDocBlocks) {
            $method->setDocBlock(
                new DocBlockGenerator(
                    'Get module configuration',
                    null,
                    array(
                        $this->generateReturnTag('array'),
                    )
                )
            );
        }

        return $method;
    }

    /**
     * Generate package tag
     *
     * @param $description
     *
     * @return Tag
     */
    protected function generatePackageTag($description)
    {
        return new Tag(
            array(
                'name'        => 'package',
                'description' => $description,
            )
        );
    }

    /**
     * Generate return tag
     *
     * @param $description
     *
     * @return Tag
     */
    protected function generateReturnTag($description)
    {
        return new Tag(
            array(
                'name'        => 'return',
                'description' => $description,
            )
        );
    }

    /**
     * Generate see tag
     *
     * @return Tag
     */
    protected function generateSeeTag()
    {
        return new Tag(
            array(
                'name'        => 'see',
                'description' => 'https://github.com/RalfEggert/FrilleZFTool',
            )
        );
    }
}
