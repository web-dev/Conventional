<?php namespace WebDev\Conventional;

use Exception;
use WebDev\Conventional\Exception\ResolverException;

/**
 * String Transformer
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
class StringTransformer
{
    public function __construct($string,$object)
    {
        $this->string = $string;
        $this->object = $object;
    }

    public function __toString()
    {
        try
        {
            return $this();
        }
        catch(Exception $exception)
        {
            return $this->string;
        }
    }

    /**
     * Invokes the string resolver transformation 
     *
     * @return string Resolt of the transformation
     */
    public function __invoke()
    {
        $resolver = new Resolver();

        $pos = 0;
        $result = "";
        do
        {
            // Start Delmiter
            $start = strpos($this->string,$this->startDelimiter,$pos);
            if($start === false)
            {
                $result .= substr($this->string,$pos);
                $pos = false;
                continue;
            }

            // End Delmiter
            $end = strpos($this->string,$this->endDelmiter,$start);
            if($end === false)
            {
                $result .= substr($this->string,$pos);
                $pos = false;
                continue;
            }

            // Resolve captured string
            $result.= substr($this->string,$pos,$start);
            $length = $end - $start;
            $path = substr($this->string,$start+1,$length-1);
            if($this->getThrowExceptions())
            {
                $result .= $resolver->get($this->object,$path);
            }
            else
            {
                try
                {
                    $result .= $resolver->get($this->object,$path);
                }
                catch(ResolverException $exception)
                {
                    $result .= substr($this->string,$start,$length+1);
                }
            }
            $pos = $end+1;
        } while($pos !== false);

        return $result;
    }

    /**
     * Delmiter that indicates the start of a transformation path 
     * 
     * @var string
     */
    protected $startDelimiter = "{";

    /**
     * Delmiter that indicates the end of a transformation path
     *
     * @var string
     */
    protected $endDelmiter = "}";

    /**
     * String to resolve using the object
     *
     * @var string
     */
    protected $string;

    /**
     * Object to use when resolving the string
     *
     * @var mixed
     */
    protected $object;

    /**
     * Indicates whether resolver exceptions should be thrown
     *
     * @var bool
     */
    protected $throwExceptions;
    public function setThrowExceptions($value){ $this->throwExceptions = $value; }
    public function getThrowExceptions(){ return $this->throwExceptions; }
}