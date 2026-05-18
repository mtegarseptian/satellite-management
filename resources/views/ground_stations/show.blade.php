@extends('layouts.admin')

@section('title', 'Ground Station Details')
@section('page-title', 'Ground Station Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ground-stations.index') }}">Ground Stations</a></li>
    <li class="breadcrumb-item active">{{ $groundStation->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center">
                    <h3 class="card-title font-weight-bold m-0">Station Information</h3>
                    <div class="ml-auto">
                        <a href="{{ route('ground-stations.edit', $groundStation) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="40%" class="text-muted">Station Name:</th>
                            <td class="font-weight-bold">{{ $groundStation->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Location:</th>
                            <td>{{ $groundStation->location }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Country:</th>
                            <td>{{ $groundStation->country }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Latitude:</th>
                            <td>{{ number_format($groundStation->latitude, 7) }}&deg;</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Longitude:</th>
                            <td>{{ number_format($groundStation->longitude, 7) }}&deg;</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Altitude:</th>
                            <td>
                                @if($groundStation->altitude)
                                    {{ $groundStation->altitude }} <small>km</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Description:</th>
                            <td>{{ $groundStation->description ?: 'No description available' }}</td>
                        </tr>
                    </table>

                    <hr>
                    <div class="row text-muted mt-2">
                        <div class="col-12 mb-1">
                            <small><i class="fas fa-calendar mr-1"></i> Created: {{ $groundStation->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div class="col-12">
                            <small><i class="fas fa-edit mr-1"></i> Updated: {{ $groundStation->updated_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold m-0">
                        <i class="fas fa-satellite text-primary mr-1"></i> 
                        Monitored Satellites ({{ $groundStation->satellites->count() }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($groundStation->satellites->count() > 0)
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-4">#</th>
                                    <th>Satellite Name</th>
                                    <th>Orbit</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groundStation->satellites as $satellite)
                                    <tr>
                                        <td class="pl-4">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold">{{ $satellite->name }}</td>
                                        <td><span class="badge badge-info">{{ $satellite->orbit_type }}</span></td>
                                        <td>
                                            @if($satellite->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('satellites.show', $satellite) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-satellite-dish fa-3x mb-3 text-light"></i>
                            <p class="mb-0">No satellites assigned to this ground station</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection