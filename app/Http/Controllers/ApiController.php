<?php

namespace App\Http\Controllers;

use App\Models\ApiTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class ApiController extends Controller
{
    public function create(){
        return view('create_api');
    }
    public function exist($table){
        $count = ApiTable::where('name', $table)->count();
        if ($count > 0){
            return 'Table already exists';
        }else{
            $api = new ApiTable();
            $api->name = $table;
            $api->save();
            return 'Table created';
        }
    }
    public function create_table($table, $col, $type){
        $sql = "CREATE TABLE $table(";
        $sql .= "id INT(6) AUTO_INCREMENT PRIMARY KEY, ";

        if (isset($col) && isset($type)){
            foreach ($col as $key => $value) {
                if ($key == count($col)-1)
                    $sql .= $value." ".$type[$key]." NOT NULL";
                else
                    $sql .= $value." ".$type[$key]." NOT NULL, ";
            }
        }
        $sql .= ", created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ";
        $sql .= "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
        return $sql;
    }
    public function make_model($table, $col){
        $file = file_exists(base_path("app/Models/{$table}.php"));
        if ($file){
            return 'Model already exists';
        }else{
            $model = fopen(base_path("app/Models/{$table}.php"), "w");
            $fillable = "protected \$fillable = [";
            foreach ($col as $key => $value) {
                if ($key == count($col)-1)
                    $fillable .= "'".$value."'";
                else
                    $fillable .= "'".$value."', ";
            }
            $fillable .= "];";
            $txt =
                "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$table} extends Model
{
    use HasFactory;
    protected \$table = '{$table}';
    {$fillable}
}
";
            fwrite($model, $txt);
            fclose($model);
            return 'Model created';
        }
    }
    public function make_controller($table){
        $file = file_exists(base_path("app/Http/Controllers/Api/{$table}Controller.php"));
        if ($file){
            return 'Controller already exists';
        }else {
            $controller = fopen(base_path("app/Http/Controllers/Api/{$table}Controller.php"), "w");
            $txt =
"<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\\$table;
use Illuminate\Http\Request;

class {$table}Controller extends Controller
{
    public function index()
    {
        \$test = {$table}::all();
        return response()->json(\$test);
    }
    public function store(Request \$request)
    {
        {$table}::create(\$request->all());
        return response()->json(['message' => '{$table} created successfully']);
    }
    public function show(\$id)
    {
        \$test = {$table}::find(\$id);
        return response()->json(\$test);
    }
    public function update(Request \$request, \$id)
    {
        \$test = {$table}::find(\$id);
        \$test->update(\$request->all());
        return response()->json(['message' => '{$table} updated successfully']);
    }
    public function destroy(\$id)
    {
        \$test = {$table}::find(\$id);
        \$test->delete();
        return response()->json(['message' => '{$table} deleted successfully']);
    }
}
";
            fwrite($controller, $txt);
            fclose($controller);
            return 'Controller created';
        }
    }
    public function make_route($table){
        $file = file_exists(base_path("routes/api.php"));
        if ($file){
            $route = fopen(base_path("routes/api.php"), "a");
            $txt ="Route::apiResource('{$table}',\App\Http\Controllers\Api\\{$table}Controller::class);\n";
            fwrite($route, $txt);
            fclose($route);
            return 'Route created';
        }else{
            return 'Route file not found';
        }
    }
    public function store(Request $request){
        $url = env("App_URL");
        $table = $request->name;
        $col = $request->col;
        $type = $request->type;
        if (!isset($col)){
            return redirect()->back()->with('error', "Please add at least one column");
        }
        $model = $this->make_model($table, $col);
        if ($model == 'Model already exists'){
            return redirect()->back()->with('error', "Model already exists");
        } else {
            $controller = $this->make_controller($table);
            $route = $this->make_route($table);
        }
        $message = $this->exist($table);
        if ($message == 'Table already exists'){
            return redirect()->back()->with('error', $message);
        }
        $sql = $this->create_table($table, $col, $type);
        $host = Config::get('database.connections.mysql.host');
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $conn = mysqli_connect($host, $username, $password, $database);
        mysqli_query($conn, $sql);
        $message = array();
        array_push($message, [
            'function' => 'index',
            'url' => $url."/api/{$table}",
            'method' => 'GET',
        ]);
        array_push($message, [
            'function' => 'store',
            'url' => $url."/api/{$table}",
            'method' => 'POST',
        ]);
        array_push($message, [
            'function' => 'show',
            'url' => $url."/api/{$table}/id",
            'method' => 'GET',
        ]);
        array_push($message, [
            'function' => 'update',
            'url' => $url."/api/{$table}/id",
            'method' => 'PUT',
        ]);
        array_push($message, [
            'function' => 'destroy',
            'url' => $url."/api/{$table}/id",
            'method' => 'DELETE',
        ]);
        return redirect()->back()->with('message', $message);
    }
}
