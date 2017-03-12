<?php

namespace App\Controllers;

use App\Services\ItemsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ItemsController
{
    /**
     * @var ItemsService
     */
    protected $itemsService;

    public function __construct($service)
    {
        $this->itemsService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->itemsService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->itemsService->getAll());
    }

    public function save(Request $request)
    {

        $note = $this->getDataFromRequest($request);
        return new JsonResponse(array("id" => $this->itemsService->save($note)));

    }

    public function update($id, Request $request)
    {
        $note = $this->getDataFromRequest($request);
        $this->itemsService->update($id, $note);
        return new JsonResponse($note);

    }

    public function delete($id)
    {

        return new JsonResponse($this->itemsService->delete($id));

    }

    public function getDataFromRequest(Request $request)
    {
        return $note = array(
            "note" => $request->request->get("note")
        );
    }
}