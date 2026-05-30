@extends('layouts.admin')

@section('title', 'Satellites')
@section('page-title', 'Satellites Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Satellites</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Satellites</h3>
            <div class="card-tools">
                <a href="{{ route('satellites.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Satellite
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('satellites.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="country" class="form-control">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="orbit" class="form-control">
                            <option value="">All Orbits</option>
                            <option value="LEO" {{ request('orbit') == 'LEO' ? 'selected' : '' }}>LEO</option>
                            <option value="MEO" {{ request('orbit') == 'MEO' ? 'selected' : '' }}>MEO</option>
                            <option value="GEO" {{ request('orbit') == 'GEO' ? 'selected' : '' }}>GEO</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('satellites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Orbit</th>
                            <th>Epoch</th> <th>Launch Date</th>
                            <th>Ground Station</th>
                            <th>Status</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($satellites as $satellite)
                            <tr>
                                <td>{{ $loop->iteration + ($satellites->currentPage() - 1) * $satellites->perPage() }}</td>
                                <td>
                                    <strong>{{ $satellite->name }}</strong>
                                </td>
                                <td>{{ $satellite->country }}</td>
                                <td><span class="badge badge-info">{{ $satellite->orbit_type }}</span></td>
                                
                                <td><small class="text-muted">{{ $satellite->epoch }}</small></td>
                                
                                <td>{{ $satellite->launch_date->format('Y-m-d') }}</td>
                                
                                <td>
                                    @if($satellite->groundStation)
                                        {{ $satellite->groundStation->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($satellite->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('satellites.show', $satellite) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('satellites.edit', $satellite) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('satellites.destroy', $satellite) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('satellites.sync-tle', $satellite) }}" method="POST" class="d-inline" onsubmit="return confirm('Tarik data TLE terbaru untuk {{ $satellite->name }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Update TLE dari Server">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No satellites found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $satellites->links() }}
            </div>
        </div>
    </div>
@endsection