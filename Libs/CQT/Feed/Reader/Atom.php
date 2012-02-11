<?php
class CQT_Feed_Reader_Atom implements CQT_Feed_Reader_Interface
{
    private $_header = array(
            'title' => '',
            'url' => '',
            'desc' => '',
            'pubdata' => ''
    );

    private $_sxe = null;

    public function __construct(SimpleXmlElement $sxe)
    {

        $this->_header['title'] = (string) $sxe->title;
        $this->_header['desc'] = (string) $sxe->summary;
        $this->_header['pubdata'] = strtotime((string) $sxe->updated);
        $this->_header['url'] = (string) $sxe->id;

        $this->_sxe = $sxe;
    }


    public function findHeader()
    {
        return $this->_header;
    }

    public function find($num = 3)
    {
        $new_entries = array();
        $entries = $this->_sxe->entry;

        for ($i=0; $i<$num; $i++) {
            if (!empty($entries[$i])) {
                $new_entries[] = array(
                                    'title' => (string) $entries[$i]->title,
                                    'url' => (string) $entries[$i]->id,
                                    'desc' => (string) $entries[$i]->summary,
                                    'pubdata' => strtotime((string) $entries[$i]->published)
                                    );
            }
        }
        return $new_entries;
    }

    public function findAll()
    {
        $entries = $this->_sxe->entry;
        $new_entries = array();

        foreach ($entries as $entry) {
            $new_entries[] = array(
                                'title' => (string) $entry->title,
                                'url' => (string) $entry->id,
                                'desc' => (string) $entry->summary,
                                'pubdata' => strtotime((string) $entry->published)
                                );
        }

        return $new_entries;
    }
    /*
    public function _parseHeader()
    {
        $this->setHeader('title', (string) $this->sxe->title);
        $this->setHeader('desc', (string) $this->sxe->summary);
        $this->setHeader('pubdata', strtotime((string) $this->sxe->updated));
        $this->setHeader('url', (string) $this->sxe->id);
    }
    */
}

