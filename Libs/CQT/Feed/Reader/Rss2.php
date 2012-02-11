<?php
class CQT_Feed_Reader_Rss2 implements CQT_Feed_Reader_Interface
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
        // ヘッダーのセット
        $this->_header['title'] = (string) $sxe->channel->title;
        $this->_header['url'] = (string) $sxe->channel->link;
        $this->_header['desc'] = (string) $sxe->channel->description;
        $this->_header['pubdata'] = strtotime((string) $sxe->channel->lastBuildDate);

        $this->_sxe = $sxe;
    }

    public function findHeader()
    {
        return $this->_header;
    }


    public function find($num = 3)
    {
        $new_entries = array();
        $entries = $this->_sxe->channel->item;

        for ($i=0; $i<$num; $i++) {
            if (!empty($entries[$i])) {
                $new_entries[] = array(
                                    'title' => (string) $entries[$i]->title,
                                    'url' => (string) $entries[$i]->link,
                                    'desc' => (string) $entries[$i]->description,
                                    'pubdata' => strtotime((string) $entries[$i]->pubDate)
                                    );
            }
        }
        return $new_entries;
    }

    public function findAll()
    {
        $entries = $this->_sxe->channel->item;
        $new_entries = array();

        foreach ($entries as $entry) {
                $new_entries[] = array(
                                    'title' => (string) $entry->title,
                                    'url' => (string) $entry->link,
                                    'desc' => (string) $entry->description,
                                    'pubdata' => strtotime((string) $entry->pubDate)
                                    );
        }

        return $new_entries;
    }
    /*
    private function _parseHeader()
    {
        $this->setHeader('title', (string) $this->sxe->channel->title);
        $this->setHeader('url', (string) $this->sxe->channel->link);
        $this->setHeader('desc', (string) $this->sxe->channel->description);
        $this->setHeader('pubdata', strtotime((string) $this->sxe->channel->lastBuildDate));
    }
    */


}