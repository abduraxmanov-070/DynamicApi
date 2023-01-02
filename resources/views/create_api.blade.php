<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <title>Create Api</title>
</head>
<body>
<div class="container p-5">

    @if( Session::has('error') )
        <div class="alert alert-danger">
            {{ Session::get('error') }}
        </div>
    @endif

    @if( Session::has('success') )
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif

    <form action="{{ route('api.store') }}" method="post" id="form">
        @csrf
        <div class="d-flex">
            <input type="text" name="name" class="form-control" placeholder="database" required>
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-info" onclick="add()">+</button>
        </div>
    </form>
    <div class="mt-3">
        @if(Session::has('message'))
            <table class="table table-bordered table-info">
                <tr>
                    <th>Function</th>
                    <th>Url</th>
                    <th>Method</th>
                </tr>
                @foreach(Session::get('message') as $msg)
                    <tr>
                        <td>{{ $msg['function'] }}</td>
                        <td>{{ $msg['url'] }}</td>
                        <td>{{ $msg['method'] }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

</div>
</body>
</html>
<script>
    function add() {
        let form = document.getElementById('form');
        let add = document.createElement('div');
        add.className = 'd-flex';
        const select = document.createElement('select');
        select.setAttribute('name', 'type[]');
        select.setAttribute('class', 'form-control form-select mt-3');
        select.setAttribute('required', 'required');
        const option1 = document.createElement('option');
        option1.setAttribute('value', 'varchar(255)');
        option1.innerHTML = 'string';
        const option2 = document.createElement('option');
        option2.setAttribute('value', 'int(11)');
        option2.innerHTML = 'integer';
        const option3 = document.createElement('option');
        option3.setAttribute('value', 'double(8, 2)');
        option3.innerHTML = 'double';
        const option4 = document.createElement('option');
        option4.setAttribute('value', 'date');
        option4.innerHTML = 'date';
        const option5 = document.createElement('option');
        option5.setAttribute('value', 'boolean');
        option5.innerHTML = 'boolean';
        select.appendChild(option1);
        select.appendChild(option2);
        select.appendChild(option3);
        select.appendChild(option4);
        select.appendChild(option5);
        add.appendChild(select);
        const col = document.createElement("input");
        col.setAttribute('class', 'form-control mt-3');
        col.setAttribute('type', 'text');
        col.setAttribute('name', 'col[]');
        col.setAttribute('required', 'true');
        add.appendChild(col);
        form.appendChild(add);
    }
</script>
