@extends('layouts.admin')

@section('title', 'Ground Stations')
@section('page-title', 'Ground Stations Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Ground Stations</li>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex align-items-center">
            <h3 class="card-title font-weight-bold m-0">List of Ground Stations</h3>
            <div class="ml-auto">
                <a href="{{ route('ground-stations.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Ground Station
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Station Name</th>
                            <th>Location</th>
                            <th>Country</th>
                            <th>Coordinates & Altitude</th>
                            <th>Satellites</th>
                            <th width="15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groundStations as $gs)
                            <tr>
                                <td>{{ $loop->iteration + ($groundStations->currentPage() - 1) * $groundStations->perPage() }}</td>
                                <td><strong>{{ $gs->name }}</strong></td>
                                <td>{{ $gs->location }}</td>
                                <td>{{ $gs->country }}</td>
                                <td>
                                    <small class="d-block mb-1">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                        {{ number_format($gs->latitude, 4) }}, {{ number_format($gs->longitude, 4) }}
                                    </small>
                                    <small class="d-block text-muted">
                                        <i class="fas fa-mountain text-secondary mr-1"></i> 
                                        Alt: {{ $gs->altitude ? $gs->altitude . ' km' : '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $gs->satellites_count }} satellites</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('ground-stations.show', $gs) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ground-stations.edit', $gs) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('ground-stations.destroy', $gs) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this ground station?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-satellite-dish fa-2x mb-2 d-block"></i>
                                    No ground stations found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $groundStations->links() }}
            </div>
        </div>
    </div>
@endsection