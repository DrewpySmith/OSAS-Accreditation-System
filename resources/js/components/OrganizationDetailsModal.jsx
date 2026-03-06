import React, { useState, useEffect } from 'react';
import * as Dialog from '@radix-ui/react-dialog';
import {
    X,
    Building2,
    Users,
    Calendar,
    FileText,
    CheckCircle2,
    Clock,
    AlertCircle,
    BarChart3,
    ArrowUpRight,
    Download,
    Mail,
    Phone,
    MapPin,
    Shield
} from 'lucide-react';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

const StatCard = ({ title, value, icon: Icon, colorClass }) => (
    <div className="bg-muted/50 rounded-xl p-4 border border-border/50 flex items-center gap-4">
        <div className={cn("w-10 h-10 rounded-lg flex items-center justify-center", colorClass)}>
            <Icon className="w-5 h-5" />
        </div>
        <div>
            <p className="text-xs text-muted-foreground font-medium uppercase tracking-wider">{title}</p>
            <p className="text-xl font-bold">{value}</p>
        </div>
    </div>
);

const OrganizationDetailsModal = ({ isOpen, onOpenChange, orgId }) => {
    const [org, setOrg] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (isOpen && orgId) {
            fetchOrgDetails();
        }
    }, [isOpen, orgId]);

    const fetchOrgDetails = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(`${window.location.origin}/admin/organizations/details/${orgId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error('Failed to fetch organization details');
            const data = await response.json();
            setOrg(data);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog.Root open={isOpen} onOpenChange={onOpenChange}>
            <Dialog.Portal>
                <Dialog.Overlay className="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] animate-in fade-in duration-300" />
                <Dialog.Content className="fixed left-[50%] top-[50%] translate-x-[-50%] translate-y-[-50%] w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-card border rounded-2xl shadow-2xl z-[101] focus:outline-none animate-in zoom-in-95 duration-200">
                    <div className="sticky top-0 bg-card/80 backdrop-blur-md border-b px-6 py-4 flex items-center justify-between z-10">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                                <Building2 className="w-6 h-6" />
                            </div>
                            <div>
                                <Dialog.Title className="text-xl font-bold tracking-tight">
                                    {loading ? 'Loading...' : org?.name}
                                </Dialog.Title>
                                <Dialog.Description className="text-xs text-muted-foreground flex items-center gap-2">
                                    <Clock className="w-3 h-3" />
                                    Viewing organization profile and status
                                </Dialog.Description>
                            </div>
                        </div>
                        <button
                            onClick={() => onOpenChange(false)}
                            className="p-2 hover:bg-muted rounded-full transition-colors text-muted-foreground hover:text-foreground"
                        >
                            <X className="w-5 h-5" />
                        </button>
                    </div>

                    <div className="p-6">
                        {loading ? (
                            <div className="flex flex-col items-center justify-center py-20 gap-4">
                                <div className="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                <p className="text-muted-foreground animate-pulse">Fetching details...</p>
                            </div>
                        ) : error ? (
                            <div className="flex flex-col items-center justify-center py-20 gap-4 text-center">
                                <div className="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center text-red-500">
                                    <AlertCircle className="w-8 h-8" />
                                </div>
                                <div>
                                    <p className="text-lg font-bold">Oops! Something went wrong</p>
                                    <p className="text-muted-foreground">{error}</p>
                                </div>
                                <button
                                    onClick={fetchOrgDetails}
                                    className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                                >
                                    Try Again
                                </button>
                            </div>
                        ) : org && (
                            <div className="space-y-8">
                                {/* Stats Section */}
                                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <StatCard
                                        title="Submissions"
                                        value={org.stats.total_submissions}
                                        icon={FileText}
                                        colorClass="bg-blue-600/10 text-blue-500"
                                    />
                                    <StatCard
                                        title="Approved"
                                        value={org.stats.approved_docs}
                                        icon={CheckCircle2}
                                        colorClass="bg-green-600/10 text-green-500"
                                    />
                                    <StatCard
                                        title="Pending"
                                        value={org.stats.pending_docs}
                                        icon={Clock}
                                        colorClass="bg-yellow-600/10 text-yellow-500"
                                    />
                                    <StatCard
                                        title="Score"
                                        value={`${org.stats.completion_percentage}%`}
                                        icon={BarChart3}
                                        colorClass="bg-purple-600/10 text-purple-500"
                                    />
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    {/* General Information */}
                                    <div className="md:col-span-2 space-y-6">
                                        <section>
                                            <h3 className="text-sm font-semibold uppercase tracking-wider text-muted-foreground mb-4">General Information</h3>
                                            <div className="bg-muted/30 rounded-xl p-5 border space-y-4">
                                                <div className="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label className="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Organization Name</label>
                                                        <p className="text-sm font-medium">{org.name}</p>
                                                    </div>
                                                    <div>
                                                        <label className="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Acronym</label>
                                                        <p className="text-sm font-medium">{org.acronym || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label className="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Description</label>
                                                    <p className="text-sm text-muted-foreground leading-relaxed">
                                                        {org.description || 'No description provided.'}
                                                    </p>
                                                </div>
                                                <div className="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label className="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Contact Email</label>
                                                        <div className="flex items-center gap-2 text-sm">
                                                            <Mail className="w-3.5 h-3.5 text-blue-500" />
                                                            <span>{org.email || 'N/A'}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label className="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Academic Year</label>
                                                        <div className="flex items-center gap-2 text-sm">
                                                            <Calendar className="w-3.5 h-3.5 text-blue-500" />
                                                            <span>{org.academic_year}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        {/* Status & Actions */}
                                        <section>
                                            <h3 className="text-sm font-semibold uppercase tracking-wider text-muted-foreground mb-4">Accreditation Status</h3>
                                            <div className={cn(
                                                "rounded-xl p-5 border flex items-center justify-between",
                                                org.is_accredited ? "bg-green-600/10 border-green-500/20" : "bg-yellow-600/10 border-yellow-500/20"
                                            )}>
                                                <div className="flex items-center gap-4">
                                                    <div className={cn(
                                                        "w-12 h-12 rounded-full flex items-center justify-center",
                                                        org.is_accredited ? "bg-green-600 text-white" : "bg-yellow-500 text-white"
                                                    )}>
                                                        {org.is_accredited ? <Shield className="w-6 h-6" /> : <AlertCircle className="w-6 h-6" />}
                                                    </div>
                                                    <div>
                                                        <p className="font-bold">
                                                            {org.is_accredited ? 'Fully Accredited' : 'Pending Accreditation'}
                                                        </p>
                                                        <p className="text-xs text-muted-foreground">
                                                            {org.is_accredited ? 'This organization has met all requirements.' : 'Some requirements are still pending review.'}
                                                        </p>
                                                    </div>
                                                </div>
                                                {org.is_accredited && (
                                                    <button className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition-colors shadow-lg shadow-green-900/20">
                                                        <Download className="w-4 h-4" />
                                                        Certificate
                                                    </button>
                                                )}
                                            </div>
                                        </section>
                                    </div>

                                    {/* Checklist Section */}
                                    <div className="space-y-6">
                                        <section>
                                            <div className="flex items-center justify-between mb-4">
                                                <h3 className="text-sm font-semibold uppercase tracking-wider text-muted-foreground">Checklist</h3>
                                                <span className="text-[10px] font-bold px-2 py-0.5 bg-blue-600/10 text-blue-500 rounded-full border border-blue-500/20">
                                                    {org.checklist.filter(i => i.is_checked).length} / {org.checklist.length}
                                                </span>
                                            </div>
                                            <div className="bg-muted/30 rounded-xl border overflow-hidden">
                                                {org.checklist.map((item, idx) => (
                                                    <div key={idx} className="flex items-start gap-3 p-3 border-b last:border-0 hover:bg-muted/50 transition-colors">
                                                        <div className={cn(
                                                            "w-5 h-5 rounded border flex items-center justify-center mt-0.5 flex-shrink-0",
                                                            item.is_checked ? "bg-blue-600 border-blue-600 text-white" : "border-border bg-card"
                                                        )}>
                                                            {item.is_checked && <CheckCircle2 className="w-3.5 h-3.5" />}
                                                        </div>
                                                        <div className="flex-1 min-w-0">
                                                            <p className={cn(
                                                                "text-xs font-medium leading-tight",
                                                                item.is_checked ? "text-foreground" : "text-muted-foreground"
                                                            )}>
                                                                {item.label}
                                                            </p>
                                                            {item.updated_at && (
                                                                <p className="text-[10px] text-muted-foreground mt-1">
                                                                    Updated {new Date(item.updated_at).toLocaleDateString()}
                                                                </p>
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </section>

                                        <a
                                            href={`${window.location.origin}/admin/organizations/view/${org.id}`}
                                            className="flex items-center justify-center gap-2 w-full py-3 bg-muted hover:bg-muted/80 rounded-xl text-sm font-bold transition-all border"
                                        >
                                            Full Dashboard
                                            <ArrowUpRight className="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </Dialog.Content>
            </Dialog.Portal>
        </Dialog.Root>
    );
};

export default OrganizationDetailsModal;
