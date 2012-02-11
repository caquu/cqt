<?php
interface CQT_Feed_Reader_Interface
{
    public function __construct(SimpleXmlElement $sxe);

    /**
     *
     * @return Array
     * array(
     *        'title' => '',
     *        'url' => '',
     *        'desc' => '',
     *        'pubdata' => ''
     * );
     */
    public function findHeader();
    public function find();
    public function findAll();

}