<?php namespace WebDev\Conventional\Exception;

class SegmentResolverException
    extends PathResolverException
{
    public function __construct($object, $path, $message=null, array $attempted=array())
    {
        if(is_null($message))
        {
            $message = "Could not access path segment `{$path}` on ".gettype($object);
            if(is_object($object))
            {
                $message .= " instance of ".get_class($object);
            }
            if(!empty($attempted))
            {
                $message .= " it was not accessable by any methods tried: `".implode('`, `', $attempted)."`";
            }
        }
        parent::__construct($object,$path,$message);
    }
}