import React, { useState } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from './ui/table';
import { Badge } from './ui/badge';
import { Eye, Pencil, Printer, Plus, Trash2, Building2, CheckCircle2, AlertCircle } from 'lucide-react';
import OrganizationActionModal from './OrganizationActionModal';
import OrganizationDetailsModal from './OrganizationDetailsModal';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

const OrganizationsList = ({ initialOrganizations, campuses, selectedCampus, printUrl, createUrl, viewUrlBase, editUrlBase, deleteUrlBase }) => {
    const [organizations, setOrganizations] = useState(initialOrganizations || []);
    const [modalOpen, setModalOpen] = useState(false);
    const [detailsOpen, setDetailsOpen] = useState(false);
    const [editingOrg, setEditingOrg] = useState(null);
    const [viewingOrgId, setViewingOrgId] = useState(null);
    const [toast, setToast] = useState({ show: false, message: '', type: 'success' });

    const showToast = (message, type = 'success') => {
        setToast({ show: true, message, type });
        setTimeout(() => setToast({ show: false, message: '', type: 'success' }), 3000);
    };

    const handleCreate = () => {
        setEditingOrg(null);
        setModalOpen(true);
    };

    const handleEdit = (org) => {
        setEditingOrg(org);
        setModalOpen(true);
    };

    const handleView = (orgId) => {
        setViewingOrgId(orgId);
        setDetailsOpen(true);
    };

    const handleSaveSuccess = (message) => {
        showToast(message);
        setTimeout(() => window.location.reload(), 1000);
    };

    const getStatusBadge = (status) => {
        if (!status) return <Badge variant="outline">Unknown</Badge>;
        switch (String(status).toLowerCase()) {
            case 'active':
                return <Badge className="bg-green-500/10 text-green-500 hover:bg-green-500/20 border-green-500/20">Active</Badge>;
            case 'inactive':
                return <Badge className="bg-red-500/10 text-red-500 hover:bg-red-500/20 border-red-500/20">Inactive</Badge>;
            case 'suspended':
                return <Badge className="bg-yellow-500/10 text-yellow-500 hover:bg-yellow-500/20 border-yellow-500/20">Suspended</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const handleCampusFilter = (e) => {
        const value = e.target.value;
        let url = new URL(window.location.href);
        if (value && value !== 'all') {
            url.searchParams.set('campus', value);
        } else {
            url.searchParams.delete('campus');
        }
        window.location.href = url.toString();
    };

    return (
        <div className="space-y-6">
            {toast.show && (
                <div className={cn(
                    "fixed top-4 right-4 z-[100] p-4 rounded-lg shadow-lg border flex items-center gap-2 animate-slide-up duration-300",
                    toast.type === 'success' ? "bg-green-500/10 border-green-500/20 text-green-500" : "bg-red-500/10 border-red-500/20 text-red-500"
                )}>
                    {toast.type === 'success' ? <CheckCircle2 className="w-4 h-4" /> : <AlertCircle className="w-4 h-4" />}
                    <span className="text-sm font-medium">{toast.message}</span>
                </div>
            )}

            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-slide-up animation-delay-100">
                <div>
                    <h2 className="text-3xl font-bold tracking-tight text-foreground">Manage Organizations</h2>
                    <p className="text-muted-foreground mt-1">View, approve, and manage registered student organizations.</p>
                </div>
                <div className="flex flex-wrap items-center gap-3">
                    <select
                        defaultValue={selectedCampus || "all"}
                        onChange={handleCampusFilter}
                        className="h-10 w-[180px] rounded-md border border-white/10 bg-card text-foreground px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="all">All Campuses</option>
                        {campuses && campuses.map(campus => (
                            <option key={campus} value={campus}>{campus}</option>
                        ))}
                    </select>

                    <a
                        href={printUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center h-10 px-4 rounded-md border border-white/10 bg-card text-foreground text-sm font-medium hover:bg-muted transition-all hover:-translate-y-0.5"
                    >
                        <Printer className="w-4 h-4 mr-2" />
                        Print List
                    </a>
                    <button
                        onClick={handleCreate}
                        className="inline-flex items-center h-10 px-4 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-all shadow-lg shadow-blue-900/20 hover:-translate-y-0.5 hover:shadow-blue-900/40"
                    >
                        <Plus className="w-4 h-4 mr-2" />
                        New Organization
                    </button>
                </div>
            </div>

            <div className="rounded-xl border bg-card text-card-foreground shadow overflow-hidden animate-slide-up animation-delay-200">
                <div className="overflow-x-auto">
                    <Table>
                        <TableHeader className="bg-muted/50">
                            <TableRow>
                                <TableHead className="w-[80px]">ID</TableHead>
                                <TableHead>Organization Details</TableHead>
                                <TableHead>Campus</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="hidden md:table-cell">Created Date</TableHead>
                                <TableHead className="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {organizations.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-32 text-center text-muted-foreground">
                                        <div className="flex flex-col items-center justify-center space-y-3 animate-fade-in animation-delay-300">
                                            <Building2 className="w-8 h-8 opacity-20" />
                                            <p>No organizations found. Create one to get started!</p>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ) : (
                                organizations.map((org, index) => (
                                    <TableRow key={org.id}
                                        className="hover:bg-muted/30 transition-colors animate-fade-in"
                                        style={{ animationDelay: `${(index % 10) * 50 + 300}ms` }}>
                                        <TableCell className="font-medium text-muted-foreground">#{org.id}</TableCell>
                                        <TableCell>
                                            <div className="flex flex-col">
                                                <span className="font-semibold text-foreground">{org.name}</span>
                                                <span className="text-sm text-muted-foreground">{org.acronym}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <span className="text-primary font-medium">{org.campus || 'N/A'}</span>
                                        </TableCell>
                                        <TableCell>
                                            {getStatusBadge(org.status)}
                                        </TableCell>
                                        <TableCell className="hidden md:table-cell text-muted-foreground text-sm">
                                            {org.created_at ? new Date(org.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <div className="flex justify-end gap-1">
                                                <button
                                                    onClick={() => handleView(org.id)}
                                                    title="View Details"
                                                    className="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-foreground hover:bg-white/5 transition-colors"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() => handleEdit(org)}
                                                    title="Edit Record"
                                                    className="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-foreground hover:bg-white/5 transition-colors"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </button>
                                                <button
                                                    title="Delete"
                                                    className="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                                    onClick={() => {
                                                        if (window.confirm('Are you sure you want to delete this organization?')) {
                                                            window.location.href = `${deleteUrlBase}/${org.id}`;
                                                        }
                                                    }}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>
            </div>

            <OrganizationActionModal
                open={modalOpen}
                onOpenChange={setModalOpen}
                record={editingOrg}
                campuses={campuses}
                onSaveSuccess={handleSaveSuccess}
            />

            <OrganizationDetailsModal
                isOpen={detailsOpen}
                onOpenChange={setDetailsOpen}
                orgId={viewingOrgId}
            />
        </div>
    );
};

export default OrganizationsList;
