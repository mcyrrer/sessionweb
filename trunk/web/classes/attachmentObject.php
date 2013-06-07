<?php

/**
 * Object of a attachment used by uploadManager.php
 */
class attachmentObject {

    private $name;
    private $size;
    private $url;
    private $thumbnail_url;
    private $delete_url;
    private $delete_type;

    public function setDeleteType($delete_type)
    {
        $this->delete_type = $delete_type;
    }

    public function getDeleteType()
    {
        return $this->delete_type;
    }

    public function setDeleteUrl($delete_url)
    {
        $this->delete_url = $delete_url;
    }

    public function getDeleteUrl()
    {
        return $this->delete_url;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setThumbnailUrl($thumbnail_url)
    {
        $this->thumbnail_url = $thumbnail_url;
    }

    public function getThumbnailUrl()
    {
        return $this->thumbnail_url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /*
     * Get a json representation of the attachment.
     */
    public function toJson()
    {
        $toJsonArray = array();
        $toJsonArray[] =  $name;
        $toJsonArray[] =  $size;
        $toJsonArray[] =  $url;
        $toJsonArray[] =  $thumbnail_url;
        $toJsonArray[] =  $delete_url;
        $toJsonArray[] =  $delete_type;
        return json_encode($toJsonArray);
    }

}
