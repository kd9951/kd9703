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
                                    Glover\Constants\LogLevel::DEBUG =>        'light',
                                    Glover\Constants\LogLevel::INFO =>         'lighinfo',
                                    Glover\Constants\LogLevel::NOTICE =>       'success',
                                    Glover\Constants\LogLevel::MEDIA_ACCESS => 'secondary',
                                    Glover\Constants\LogLevel::JOB =>          'dark',
                                    Glover\Constants\LogLevel::WARNING =>      'warning',
                                    Glover\Constants\LogLevel::ERROR =>        'danger',
                                    Glover\Constants\LogLevel::CRITICAL =>     'danger',
                                    Glover\Constants\LogLevel::ALERT =>        'danger',
                                    Glover\Constants\LogLevel::EMERGENCY =>    'danger',
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
