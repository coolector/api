<?php

namespace App\Services;

use App\Collectors\BuilderSwitcher;
use App\Parser\ItemParser;

class ItemsService extends BaseService
{
    public function getOne($id)
    {
        return $this->db->fetchAssoc("SELECT * FROM items WHERE id=?", [(int) $id]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM items");
    }

    function save($data)
    {
        $parser = new ItemParser($data);
        $requestItem = $parser->parse();

        $switcher = new BuilderSwitcher($requestItem->getUrl());
        $builder = $switcher->getBuilder();

        if (null === $builder) {
            throw new \RuntimeException(sprintf('Could not find a suitable builder for url %s', $requestItem->getUrl()));
        }

        $item = $builder->build($requestItem);

//        $this->db->insert("items", $item);
        return $this->db->lastInsertId();
    }

    function update($id, $note)
    {
//        return $this->db->update('items', $note, ['id' => $id]);
    }

    function delete($id)
    {
//        return $this->db->delete("items", array("id" => $id));
    }
}