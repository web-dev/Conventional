<?php namespace WebDev\Conventional;

use ArrayAccess;
use ReflectionClass;
use WebDev\Conventional\Exception\PathResolverException;
use WebDev\Conventional\Exception\ResolverException;
use WebDev\Conventional\Exception\SegmentResolverException;

/**
 * Resolves properties within objects and arrays according to a set of convertions
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
class Resolver
{
    /**
     * Prefixes that will be tried when attempting to check for the presence of a variable
     * 
     * @var string[]
     */
    protected $presenceIndicatorPrefixes = array('has','is','get');

    /**
     * Prefixes that will be tried when attempting to access a variable path
     * 
     * @var string[]
     */
    protected $accessorPrefixes = array('get','is');

    /**
     * Prefixes that will be tried when attempting to modify a variable path
     *
     * @var string[]
     */
    protected $modifierPrefixes = array('set');

    /**
     * Delimiter that is used to separate segements of the path
     * 
     * @var string
     */
    const DELIMITER = ".";

    const ARRAY_BEGIN = "[";
    const ARRAY_END = "]";

    /**
     * Resolves the path segment as an accessor
     * 
     * @param mixed $object Variable to resolve from
     * @param string $path Path to resolve the variable on
     * @return mixed resolved value
     */
    public function get($object, $path)
    {
        // Array only resolution
        if(substr($path,0,1) === self::ARRAY_BEGIN)
        {
            if(!is_array($current) && !($current instanceof ArrayAccess))
            {
                throw new SegmentResolverException($object,$path);
            }
            $terminator = strpos($path,self::ARRAY_END,1);
            if($terminator === false)
            {
                throw new SegmentResolverException($object,$path,
                    "Could not find a matching closing bracket for array accessor in path.");
            }

            $segment = substr($path,1,$terminator);
            $remainder = substr($path,$terminator+1);

            if(isset($object[$segment]))
            {
                $target = $object[$segment];
            }
            else
            {
                $target = null;
            }
        }
        else
        {
            $terminator = min(strpos(self::DELIMITER,$path),strpos(self::ARRAY_BEGIN,$path));
            if($terminator === false)
            {
                $segment = $path;
            }
            else
            {
                $segment = substr($path,0,$terminator);
            }

            // Remainder depends on the type of the next component of the path
            if($terminator === false)
            {
                $remainder = "";
            }
            elseif(substr($path,$terminator,1)===self::ARRAY_BEGIN)
            {
                $remainder = substr($path,$terminator);
            }
            else
            {
                $remainder = substr($path,$terminator+1);
            }

            // Attempt to access the specified segment
            if(is_array($object))
            {
                $target = array_key_exists($segment,$object) ? $object[$segment] : null;
            }
            elseif(is_object($object))
            {
                $class = new ReflectionClass($object);
                $property = $class->hasProperty($segment) ? $class->getProperty($segment) : null;
                if(!is_null($property) && $property->isPublic())
                {
                    $target = $property->getValue($object);
                }
                else
                {
                    $methodsAttempted = array();
                    $matched = false;
                    foreach($this->accessorPrefixes as $prefix)
                    {
                        $method = $prefix.ucfirst($segment);
                        if(is_callable(array($object,$method)))
                        {
                            $target = call_user_func(array($object,$method));
                            $matched = true;
                            break;
                        }
                        else
                        {
                            $methodsAttempted[] = "$method()";
                        }
                    }
                    if(!$matched)
                    {
                        if($object instanceof ArrayAccess)
                        {
                            if(isset($object[$segment]))
                            {
                                $target = $object[$segment];
                            }
                            else
                            {
                                $target = null;
                            }
                        }
                        else
                        {
                            throw new SegmentResolverException($object,$path,null,$methodsAttempted);
                        }
                    }
                }
            }
            else
            {
                throw new SegmentResolverExcetpion($object,$path);
            }
        }

        if(strlen($remainder)>0)
        {
            return $this->get($object,$remainder);
        }
        else
        {
            return $target;
        }
    }
}