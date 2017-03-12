<?php

namespace Tests\Services;
use App\Services\ItemsService;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;


class ItemsServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ItemsService
     */
    private $itemsServices;

    public function setUp()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            "db.options" => array(
                "driver" => "pdo_sqlite",
                "memory" => true
            ),
        ));
        $this->itemsServices = new ItemsService($app["db"]);

        $schema = file_get_contents(__DIR__ . '/../../resources/sql/schema.sql');
        $schema = str_replace(array("\r", "\n"), '', $schema);
        $sqls = array_filter(explode(';', $schema));
        array_map(function ($sql) use ($app) {
            $app["db"]->executeQuery($sql);
        }, $sqls);

//        $stmt = $app["db"]->prepare("INSERT INTO items (url, title, image, created_at, updated_at) VALUES ('http://www.google.com', 'some title', '.jpg', '2016-01-01 10:10', '2016-01-01 10:10')");
//        $stmt->execute();
    }

    public function testGetOne()
    {
        $data = $this->itemsServices->getOne(1);
        $this->assertEquals('dummynote', $data['note']);
    }

    public function testGetAll()
    {
        $data = $this->noteService->getAll();
        $this->assertNotNull($data);
    }

    function testSave()
    {
        $item = array(
            "url" => "https://www.instagram.com",
            "collectors" => [
                [
                    "name" => "in_view_selector",
                    "value" => [
                        [
                            "name" => "img",
                            "attributes" => ["src" => "https://scontent-fra3-1.cdninstagram.com/t51.2885-15/e35/17266146_1118875661557398_3994486560723566592_n.jpg"],
                            "content" => null,
                            "visibility" => 0.8
                        ],
                        [
                            "name" => "img",
                            "attributes" => ["src" => "https://scontent-fra3-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/17126274_1206425536141410_5539125815918198784_n.jpg"],
                            "content" => null,
                            "visibility" => 0.2
                        ]
                    ]
                ],
                [
                    "name" => "parents_selector",
                    "value" => [
                        [
                            "name" => "img",
                            "attributes" => ["src" => "https://scontent-fra3-1.cdninstagram.com/t51.2885-15/e35/17266146_1118875661557398_3994486560723566592_n.jpg"],
                            "content" => null,
                            "parents" => [
                                [
                                    "name" => "article",
                                    "attributes" => []
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "name" => "children_selector",
                    "value" => [
                        [
                            [
                                "name" => "article",
                                "attributes" => [],
                                "children" => [
                                    [
                                        "name" => "a",
                                        "attributes" => ["href" => "/p/BRgqLUsD87r/"]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        $data = $this->itemsServices->save($item);
        $this->assertEquals(2, $data);
    }

    function testUpdate()
    {
        $note = array("note" => "arny1");
        $this->noteService->save($note);
        $note = array("note" => "arny2");
        $this->noteService->update(1, $note);
        $data = $this->noteService->getAll();
        $this->assertEquals("arny2", $data[0]["note"]);

    }

    function testDelete()
    {
        $note = array("note" => "arny1");
        $this->noteService->save($note);
        $this->noteService->delete(1);
        $data = $this->noteService->getAll();
        $this->assertEquals(1, count($data));
    }

}
