import React, { useState, useEffect } from 'react';
import { Badge } from './ui/badge';
import { AlertCircle, CheckCircle2, RefreshCw, XCircle, Search, HelpCircle, ArrowRight } from 'lucide-react';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

const StatisticsDashboard = ({ campuses }) => {
    const [selectedCampus, setSelectedCampus] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState(null);
    const [selectedOrgDetails, setSelectedOrgDetails] = useState(null);

    // Fetch stats on campus change
    const fetchStats = async (campus) => {
        setLoading(true);
        try {
            const response = await fetch(`/admin/statistics/dashboard-data?campus=${campus}`);
            const data = await response.json();
            if (data.success) {
                setStats(data);
            }
        } catch (error) {
            console.error('Error fetching statistics:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchStats(selectedCampus);
    }, [selectedCampus]);

    // Handle search query matching org name/acronym
    const filteredOrgs = stats?.organizations?.filter(org => 
        org.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
        org.acronym.toLowerCase().includes(searchTerm.toLowerCase())
    ) || [];

    // Accreditation rate percentage
    const accreditationRate = stats?.total_orgs > 0 
        ? Math.round((stats.accredited_count / stats.total_orgs) * 100) 
        : 0;

    return (
        <div className="space-y-6">
            {/* Filter Bar & Search */}
            <div className="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-card p-4 rounded-xl border border-white/5 animate-slide-up animation-delay-100">
                <div className="flex flex-wrap items-center gap-3">
                    <span className="text-sm font-medium text-muted-foreground">Filter Campus:</span>
                    <select
                        value={selectedCampus}
                        onChange={(e) => setSelectedCampus(e.target.value)}
                        className="h-10 w-[200px] rounded-md border border-white/10 bg-background text-foreground px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="all">All Campuses</option>
                        {campuses && campuses.map(campus => (
                            <option key={campus} value={campus}>{campus}</option>
                        ))}
                    </select>
                    
                    <button 
                        onClick={() => fetchStats(selectedCampus)} 
                        disabled={loading}
                        className="inline-flex items-center justify-center w-10 h-10 rounded-md border border-white/10 hover:bg-muted text-muted-foreground hover:text-foreground transition-all disabled:opacity-50"
                        title="Reload Stats"
                    >
                        <RefreshCw className={cn("w-4 h-4", loading && "animate-spin")} />
                    </button>
                </div>

                <div className="relative w-full sm:w-[260px]">
                    <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Search organizations..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="h-10 w-full pl-9 pr-4 rounded-md border border-white/10 bg-background text-foreground text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                    />
                </div>
            </div>

            {loading ? (
                <div className="flex flex-col items-center justify-center h-64 space-y-4">
                    <RefreshCw className="w-8 h-8 text-blue-500 animate-spin" />
                    <p className="text-sm text-muted-foreground">Gathering statistics and checklists...</p>
                </div>
            ) : (
                <>
                    {/* Modern Dynamic Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 animate-slide-up animation-delay-200">
                        {/* Total Organizations Card */}
                        <div className="relative rounded-2xl border border-white/5 bg-card/45 backdrop-blur-md p-6 overflow-hidden">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total Organizations</p>
                                    <h3 className="text-4xl font-extrabold text-white mt-2">{stats?.total_orgs || 0}</h3>
                                </div>
                                <div className="p-3 bg-white/5 rounded-xl border border-white/5 text-blue-400">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-4">Active & registered inside the selected campus filter.</p>
                        </div>

                        {/* Accredited Organizations Card */}
                        <div className="relative rounded-2xl border border-white/5 bg-card/45 backdrop-blur-md p-6 overflow-hidden">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Accredited Organizations</p>
                                    <h3 className="text-4xl font-extrabold text-emerald-400 mt-2">{stats?.accredited_count || 0}</h3>
                                </div>
                                <div className="p-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20 text-emerald-400">
                                    <CheckCircle2 className="w-6 h-6" />
                                </div>
                            </div>
                            <div className="flex items-center gap-2 mt-4">
                                <div className="flex-1 bg-white/5 h-1.5 rounded-full overflow-hidden">
                                    <div className="bg-emerald-500 h-full rounded-full transition-all duration-500" style={{ width: `${accreditationRate}%` }}></div>
                                </div>
                                <span className="text-xs font-bold text-emerald-400">{accreditationRate}% rate</span>
                            </div>
                        </div>

                        {/* Non-Accredited Organizations Card */}
                        <div className="relative rounded-2xl border border-white/5 bg-card/45 backdrop-blur-md p-6 overflow-hidden">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Non-Accredited Orgs</p>
                                    <h3 className="text-4xl font-extrabold text-rose-400 mt-2">{stats?.unaccredited_count || 0}</h3>
                                </div>
                                <div className="p-3 bg-rose-500/10 rounded-xl border border-rose-500/20 text-rose-400">
                                    <XCircle className="w-6 h-6" />
                                </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-4">Organizations missing one or more of the 9 required docs.</p>
                        </div>
                    </div>

                    {/* Organization Accreditation Progress List */}
                    <div className="rounded-xl border border-white/5 bg-card overflow-hidden animate-slide-up animation-delay-300">
                        <div className="p-6 border-b border-white/5">
                            <h3 className="text-lg font-bold text-white">Accreditation Metrics & Document Compliance</h3>
                            <p className="text-xs text-muted-foreground mt-1">Shows precise accreditation progress and missing checklist criteria per organization.</p>
                        </div>
                        <div className="divide-y divide-white/5">
                            {filteredOrgs.length === 0 ? (
                                <div className="p-12 text-center text-muted-foreground">
                                    No organizations match your query.
                                </div>
                            ) : (
                                filteredOrgs.map(org => {
                                    const isAccredited = org.progress === 100;
                                    return (
                                        <div key={org.id} className="p-6 hover:bg-white/[0.02] transition-colors flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            {/* Org Name & Acronym */}
                                            <div className="w-full md:w-[35%]">
                                                <div className="flex items-center gap-2">
                                                    <span className="font-semibold text-white text-base leading-tight">{org.name}</span>
                                                    <Badge className="bg-white/5 text-muted-foreground border-white/10">{org.acronym}</Badge>
                                                </div>
                                                <p className="text-xs text-muted-foreground mt-1.5">{org.campus} Campus &bull; ID: #{org.id}</p>
                                            </div>

                                            {/* Progress Bar */}
                                            <div className="flex-1 w-full">
                                                <div className="flex justify-between items-center text-xs mb-1.5">
                                                    <span className="font-medium text-muted-foreground">Accreditation Requirements Verified</span>
                                                    <span className={cn("font-bold", isAccredited ? "text-emerald-400" : "text-blue-400")}>{org.progress}%</span>
                                                </div>
                                                <div className="bg-white/5 h-2 rounded-full overflow-hidden">
                                                    <div 
                                                        className={cn(
                                                            "h-full rounded-full transition-all duration-500",
                                                            isAccredited ? "bg-emerald-500 shadow-md shadow-emerald-500/25" : "bg-blue-500 shadow-md shadow-blue-500/25"
                                                        )} 
                                                        style={{ width: `${org.progress}%` }}
                                                    ></div>
                                                </div>
                                                <p className="text-[11px] text-muted-foreground mt-1">Calculated strictly out of 9 required items.</p>
                                            </div>

                                            {/* Status and Detail Triggers */}
                                            <div className="w-full md:w-[25%] flex flex-col sm:flex-row items-start sm:items-center md:justify-end gap-3">
                                                <div>
                                                    {isAccredited ? (
                                                        <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">Accredited</span>
                                                    ) : (
                                                        <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/25">Unaccredited</span>
                                                    )}
                                                </div>

                                                {!isAccredited && (
                                                    <button
                                                        onClick={() => setSelectedOrgDetails(org)}
                                                        className="inline-flex items-center gap-1 text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-2.5 py-1 rounded-md transition-colors"
                                                    >
                                                        Why unaccredited?
                                                        <ArrowRight className="w-3 h-3" />
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </div>
                </>
            )}

            {/* Missing Requirements Glassmorphic Modal */}
            {selectedOrgDetails && (
                <div className="fixed inset-0 bg-black/75 backdrop-blur-md flex items-center justify-center z-50 p-4 transition-all animate-fade-in">
                    <div className="bg-slate-900 border border-white/10 text-white rounded-2xl p-6 w-full max-w-md shadow-2xl transform scale-100 transition-all">
                        <div className="flex items-center justify-between border-b border-white/5 pb-4 mb-4">
                            <div className="flex items-center gap-2.5 text-rose-400">
                                <AlertCircle className="w-5 h-5" />
                                <h3 className="font-bold text-lg leading-tight">Accreditation Gaps</h3>
                            </div>
                            <button 
                                onClick={() => setSelectedOrgDetails(null)} 
                                class="text-muted-foreground hover:text-white text-2xl font-bold leading-none"
                            >
                                &times;
                            </button>
                        </div>

                        <p className="text-sm text-slate-300 mb-4 font-semibold leading-snug">
                            {selectedOrgDetails.name} ({selectedOrgDetails.acronym})
                        </p>

                        <p className="text-xs text-muted-foreground mb-4">
                            Accreditation hits 100% ONLY when all 9 mandatory requirements are verified by the USG Administration. The following requirements are still missing or unverified:
                        </p>

                        {/* Missing list */}
                        <div className="space-y-2 mb-6">
                            {selectedOrgDetails.missing_requirements.map((req, index) => (
                                <div key={index} className="flex items-start gap-2.5 text-xs text-rose-300 bg-rose-500/5 border border-rose-500/10 p-2.5 rounded-lg">
                                    <span className="font-bold">&bull;</span>
                                    <span>{req}</span>
                                </div>
                            ))}
                        </div>

                        <button 
                            onClick={() => setSelectedOrgDetails(null)} 
                            className="w-full h-10 rounded-md bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition-colors"
                        >
                            Close Details
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default StatisticsDashboard;
