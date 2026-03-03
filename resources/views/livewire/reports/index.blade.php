<div>
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-text mb-2">Reports</h1>
            <p class="text-muted">View and analyze issue data with detailed reports</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Monthly Report -->
            <a href="{{ route('reports.monthly') }}" class="card hover:border-primary transition-smooth cursor-pointer group">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-primary/20 flex items-center justify-center text-primary group-hover:bg-primary/30 transition-smooth">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-text">Monthly Report</h3>
                            <p class="text-sm text-muted">Detailed monthly breakdown</p>
                        </div>
                    </div>
                    <p class="text-sm text-muted">View issue statistics, trends, and breakdowns by status, department, and type for a specific month.</p>
                </div>
            </a>

            <!-- Yearly Report -->
            <a href="{{ route('reports.yearly') }}" class="card hover:border-primary transition-smooth cursor-pointer group">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-accent/20 flex items-center justify-center text-accent group-hover:bg-accent/30 transition-smooth">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-text">Yearly Report</h3>
                            <p class="text-sm text-muted">Annual overview and trends</p>
                        </div>
                    </div>
                    <p class="text-sm text-muted">Comprehensive yearly analysis with monthly trends and performance metrics.</p>
                </div>
            </a>

            <!-- Logbook Report -->
            <a href="{{ route('reports.logbook') }}" class="card hover:border-primary transition-smooth cursor-pointer group">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-warning/20 flex items-center justify-center text-warning group-hover:bg-warning/30 transition-smooth">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-text">Logbook</h3>
                            <p class="text-sm text-muted">Printable issue list</p>
                        </div>
                    </div>
                    <p class="text-sm text-muted">Generate and print filtered logbook reports with export capabilities.</p>
                </div>
            </a>
        </div>
    </div>
</div>
