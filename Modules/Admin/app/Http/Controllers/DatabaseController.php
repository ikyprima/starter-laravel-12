<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\DataType;
use Modules\Admin\Database\Schema\Identifier;
use Modules\Admin\database\Schema\SchemaManager;
use Modules\Admin\database\Schema\Table;
use Modules\Admin\database\Types\Type;
use Illuminate\Support\MessageBag;
use Str;
use DB;
use Inertia\Inertia;
use Inertia\Response;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('admin/databases/Index');
    }

    public function create(): Response
    {
        $db = $this->prepareDbManager('create');
        return Inertia::render('admin/databases/Form', [
            'db' => $db
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        // $newtable = [
        //     'name' => 'tes_table_master_dua',
        //     // 'oldName' => '',
        //     'columns' => [
        //         [
        //             'name' => 'id',
        //             // 'oldName' => null,
        //             'type' => [
        //                 'name' => 'integer',
        //                 'category' => 'Numbers',
        //                 'default' => [
        //                     'type' => 'number',
        //                     'step' => 'any',
        //                 ],
        //             ],
        //             'length' => null,
        //             'fixed' => false,
        //             'unsigned' => true,
        //             'autoincrement' => true,
        //             'notnull' => true,
        //             'default' => null,
        //         ],
        //         [
        //             'name' => 'nama',
        //             // 'oldName' => null,
        //             'type' => [
        //                 'name' => 'text',
        //                 'category' => 'Strings',
        //                 'notSupportIndex' => true,
        //                 'default' => [
        //                     'disabled' => true,
        //                 ],
        //             ],
        //             'length' => null,
        //             'fixed' => false,
        //             'unsigned' => false,
        //             'autoincrement' => false,
        //             'notnull' => false,
        //             'default' => null,
        //         ],
        //          [
        //             'name' => 'id_kategori',
        //             // 'oldName' => null,
        //             'type' => [
        //                 'name' => 'integer',
        //                 'category' => 'Numbers',
        //                 'default' => [
        //                     'type' => 'number',
        //                     'step' => 'any',
        //                 ],
        //             ],
        //             'length' => null,
        //             'fixed' => false,
        //             'unsigned' => false,
        //             'autoincrement' => false,
        //             'notnull' => false,
        //             'default' => null,
        //         ],
                
        //     ],
        //     'indexes' => [
        //         [
        //             'indexColumns' => 0,
        //             'columns' => ['id'],
        //             'type' => 'PRIMARY',
        //             'name' => 'primary',
        //             'table' => 'test_table',
        //         ],
        //     ],
        //     'primaryKeyName' => 'primary',
        //     'foreignKeys' => [
        //         [
        //             'name' => 'fk_kategori_master',
        //             'localColumns' => ['id_kategori'],
        //             'foreignTable' => 'tes_master',
        //             'foreignColumns' => ['id'],
        //             'options' => [
        //                 'onDelete' => 'SET NULL',
        //                 'onUpdate' => 'CASCADE',
        //             ],
        //         ],
        //     ],
        //     'options' => [
        //         'create_options' => [],
        //     ],
        // ];

        try {

            $conn = 'database.connections.'.config('database.default');
            Type::registerCustomPlatformTypes();
            $table = $request->all();
            $table['options']['collate'] = config($conn.'.collation', 'utf8mb4_unicode_ci');
            $table['options']['charset'] = config($conn.'.charset', 'utf8mb4');
            $table['fkConstraints']=[];
            
            $table = Table::make($table);

            SchemaManager::createTable($table);


            return to_route('admin.database-manager.index')->with(['message'=>'Sukses Simpan Data']);
            
        } catch(\Illuminate\Database\QueryException $e){
            $text= $e->getMessage();
            $errors = new MessageBag(['error' => [$e->errorInfo[2]]]);
            return back()->withErrors($errors);
        }
    

    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('admin::show');
    }

    public function edit($id): Response
    {
        $db = $this->prepareDbManager('update', $id);
        return Inertia::render('admin/databases/Form', [
            'db' => $db
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $table = $request->all();
            \Modules\Admin\database\DatabaseUpdater::update($table);

            return to_route('admin.database-manager.index')->with(['message' => 'Sukses Update Data']);
        } catch (\Exception $e) {
            $errors = new MessageBag(['error' => [$e->getMessage()]]);
            return back()->withErrors($errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            SchemaManager::dropTable($id);
            return to_route('admin.database-manager.index')->with(['message' => 'Sukses Hapus Data']);
        } catch (\Exception $e) {
             $errors = new MessageBag(['error' => [$e->getMessage()]]);
            return back()->withErrors($errors);
        }
    }

    public function listTable() {
        try {
             $dataTypes = DataType::select('id', 'name', 'slug')
            ->get()
            ->keyBy('name');

            // list tabel dari Doctrine, dikurangi tabel yg dikecualikan
            $tables = collect(SchemaManager::listTableNames())
                ->diff(config('admin.tabelList'))   
                ->map(function ($table) use ($dataTypes) {
                    $cleanName = Str::replaceFirst(DB::getTablePrefix(), '', $table);

                    return (object) [
                        'prefix'     => DB::getTablePrefix(),
                        'name'       => $cleanName,
                        'slug'       => $dataTypes[$cleanName]['slug'] ?? null,
                        'dataTypeId' => $dataTypes[$cleanName]['id'] ?? null,
                    ];
                });

            return response()->json($tables->values());
            
        } catch(\Illuminate\Database\QueryException $e){
            $text= $e->getMessage();
            return response()->json([
                'message' => $text,
                'error' => [$e->errorInfo]
            ]);
        }
    }
    protected function prepareDbManager($action, $table = '')
    {
        $db = new \stdClass();

        // Need to get the types first to register custom types
        $type = Type::getPlatformTypes();
        $db->types =$type;

        if ($action == 'update') {
            $indexes = collect();
            $foreignKeys = collect();
            $columns = SchemaManager::describeTable($table)
            ->map(function($item,$key)use($type,&$indexes,&$foreignKeys,$table){
                $nametype = $item['type'];
                $objectType = $type->flatMap(function ($items) use ($nametype) {
                    return collect($items)->filter(function ($item) use ($nametype) {
                        return $item['name'] === $nametype;
                    });
                })->first();

                collect($item['indexes'])->values()->map(function($item) use(&$indexes, $table){
                    $item["table"] = $table;
                    $indexes->push($item);
                });
                
                // collect($item['foreign'])->map(function($fk) use (&$foreignKeys) {
                //     $foreignKeys->push([
                //         'name' => $fk['name'],
                //         'localColumns' => $fk['localColumns'],
                //         'foreignTable' => $fk['foreignTable'],
                //         'foreignColumns' => $fk['foreignColumns'],
                //         'options' => $fk['options'] ?? [],
                //     ]);
                // });
                collect($item['foreign'] ? [$item['foreign']] : [])->map(function($fk) use (&$foreignKeys, $item) {
                    $foreignKeys->push([
                        'name'           => $fk['name'],
                        'localColumns'   => [$item['name']],  // ambil kolomnya
                        'foreignTable'   => $fk['foreignTable'],
                        'foreignColumns' => $fk['foreignColumns'],
                        'options'        => $fk['options'] ?? [],
                    ]);
                });
                
                return [
                    'name' => $item['name'],
                    'oldName' => $item['name'],
                    'type' => $objectType,
                    'length'=>$item['length'],
                    'precision'=>$item['precision'],
                    'scale'=>$item['scale'],
                    'fixed'=> $item['fixed'],
                    'unsigned'=> $item['unsigned'],
                    'autoincrement'=> $item['autoincrement'],
                    'notnull'=> $item['notnull'],
                    'default'=> $item['default'],
                ] ;
            })->values();

            $varTable = [
                'name'=>$table,
                'oldName' => $table,
                'columns' => $columns,
                'indexes' => $indexes,
                'primaryKeyName'=> 'primary',
                'foreignKeys'=> $foreignKeys,
                'options' => [
                    'create_options' => []
                ]
                    

            ];
            $db->table = $varTable;
            // $db->table = SchemaManager::listTableDetails($table);
            $db->formAction = route('database.update', $table);
        } else {
            $db->table = [
                'name' => '',
                'oldName' => '',
                'columns' => [
                    [
                        'name' => 'id',
                        'type' => ['name' => 'integer', 'category' => 'Numbers'],
                        'length' => null,
                        'precision' => null,
                        'scale' => null,
                        'fixed' => false,
                        'unsigned' => true,
                        'autoincrement' => true,
                        'notnull' => true,
                        'default' => null,
                    ]
                ],
                'indexes' => [],
                'foreignKeys' => [],
                'options' => ['create_options' => []],
            ];

            $db->formAction = route('admin.database-manager.store');
        }

        $oldTable = old('table');
        $db->oldTable = $oldTable ? $oldTable : json_encode(null);
        $db->action = $action;
        $db->identifierRegex = Identifier::REGEX;
        // $db->platform = SchemaManager::getDatabasePlatform()->getName();
        $db->platform = getDatabasePlatformName();

        return $db;
    }
}
