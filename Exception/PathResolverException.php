<?php namespace WebDev\Conventional\Exception;

class PathResolverException
    extends ResolverException
{
    public function __construct($object, $path, $message=null)
    {
        $this->object = $object;
        $this->path = $path;

        if(is_null($message))
        {
            $message = "Could not access path `{$path}` on ".gettype($object);
            if(is_object($object))
            {
                $message .= " instance of ".get_class($object);
            }
        }
        parent::__construct($message);
    }

    /**
     * Parent object on which the resolver exception was encountered
     *
     * @var string
     */
    protected $object;
    public function getObject() { return $this->object; }

    /**
     * Path where the exception occured
     *
     * @var string
     */
    protected $path;
    public function getPath() { return $this->path; }
}