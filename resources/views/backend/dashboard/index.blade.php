@extends('backend.layout-backend')

@section('content')
    <section class="py-4 bg-gray-50 flex-grow px-6">
        <div class="container">
            <h1 class="text-4xl font-extrabold text-gray-900 text-center mb-6">Welcome to TALENTAKU</h1>
            <p class="text-lg text-gray-600 text-center mb-8">Your platform for managing classrooms, students, and reports with ease.</p>

            <div class="row">
                <!-- Total Students Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="card-text text-green-600 font-bold">{{ $totalStudents }}</h2>
                            <p class="text-gray-500">Students enrolled in the system</p>
                        </div>
                    </div>
                </div>

                <!-- Total Classrooms Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Classrooms</h5>
                            <h2 class="card-text text-blue-600 font-bold">{{ $totalClassrooms }}</h2>
                            <p class="text-gray-500">Classrooms available for students</p>
                        </div>
                    </div>
                </div>

                <!-- Total Reports Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Teacher</h5>
                            <h2 class="card-text text-orange-600 font-bold">{{ $totalTeacher }}</h2>
                            <p class="text-gray-500">Teachers currently active in the system</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
