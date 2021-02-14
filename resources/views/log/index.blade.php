@extends('layouts.app')

@section('content')
        <div class="container">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="card-title mb-0">{{$logname}}</h4>
                            <div class="small text-muted">Logs</div>
                        </div>
                        {{-- <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                            <a  href="{{route('owner_logs.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                        </div> --}}
                    </div>
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>created_at</th>
                                <th>level</th>
                                <th>job_id</th>
                                <th>owner_id</th>
                                <th>message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td><a href="{{route($logname . '.show', [substr($logname,0,strlen($logname)-1)=>$log['id']])}}">{{$log['created_at']}}</a></td>
                                <td><span class="badge bg-{{[
                                    Kd9703\Constants\LogLevel::DEBUG =>        'light',
                                    Kd9703\Constants\LogLevel::INFO =>         'light',
                                    Kd9703\Constants\LogLevel::NOTICE =>       'success',
                                    Kd9703\Constants\LogLevel::MEDIA_ACCESS => 'secondary',
                                    Kd9703\Constants\LogLevel::JOB =>          'dark',
                                    Kd9703\Constants\LogLevel::WARNING =>      'warning',
                                    Kd9703\Constants\LogLevel::ERROR =>        'danger',
                                    Kd9703\Constants\LogLevel::CRITICAL =>     'danger',
                                    Kd9703\Constants\LogLevel::ALERT =>        'danger',
                                    Kd9703\Constants\LogLevel::EMERGENCY =>    'danger',
                                ][$log['level']]}}">{{$log['level']}}</span></td>
                                <td>{{$log['job_id']}}</td>
                                <td>{{$log['owner_id']}}</td>
                                <td>{{mb_strimwidth($log['message'], 0, 120, '......')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $logs->links() }}



        </div>
@endsection
