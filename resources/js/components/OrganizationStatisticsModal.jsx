import React, { useState, useEffect } from 'react';
import * as Dialog from '@radix-ui/react-dialog';
import { X, BarChart2, FileText, TrendingUp, Calendar, Download, Printer, ExternalLink } from 'lucide-react';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

const Section = ({ title, icon: Icon, children }) => (
    <div className="space-y-3">
        <div className="flex items-center gap-2">
            <Icon className="w-4 h-4 text-blue-400" />
            <h4 className="font-semibold text-foreground">{title}</h4>
        </div>
        {children}
    </div>
);

const EmptyRow = ({ cols, message }) => (
    <tr>
        <td colSpan={cols} className="text-center text-muted-foreground py-6 text-sm">{message}</td>
    </tr>
);

const TableWrapper = ({ heads, children }) => (
    <div className="overflow-x-auto rounded-xl border border-white/10">
        <table className="w-full text-sm">
            <thead className="bg-muted/50">
                <tr>
                    {heads.map((h, i) => (
                        <th key={i} className="h-10 px-4 text-left font-medium text-muted-foreground whitespace-nowrap">{h}</th>
                    ))}
                </tr>
            </thead>
            <tbody>{children}</tbody>
        </table>
    </div>
);

const OrganizationStatisticsModal = ({ open, onOpenChange, orgId }) => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        if (!open || !orgId) return;
        setLoading(true);
        setError('');
        setData(null);

        fetch(`/admin/statistics/organization-data/${orgId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    setData(res);
                } else {
                    setError(res.message || 'Failed to load data.');
                }
            })
            .catch(() => setError('An unexpected error occurred.'))
            .finally(() => setLoading(false));
    }, [open, orgId]);

    const org = data?.organization;

    return (
        <Dialog.Root open={open} onOpenChange={onOpenChange}>
            <Dialog.Portal>
                <Dialog.Overlay className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 animate-fadeIn" />
                <Dialog.Content
                    className="fixed left-[50%] top-[50%] z-50 w-full max-w-4xl max-h-[90vh] bg-card rounded-3xl shadow-2xl overflow-hidden border border-white/10 text-card-foreground animate-modalSlideIn focus:outline-none flex flex-col"
                    style={{ transform: 'translate(-50%, -50%)' }}
                >
                    {/* Header */}
                    <div className="relative bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 shrink-0">
                        <Dialog.Close className="absolute top-5 right-6 text-white/80 hover:text-white transition-colors outline-none">
                            <X className="w-6 h-6" />
                        </Dialog.Close>
                        <div className="flex items-center gap-3 pr-10">
                            <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md shrink-0">
                                <BarChart2 className="w-6 h-6 text-white" />
                            </div>
                            <div className="min-w-0">
                                <Dialog.Title className="text-xl font-bold text-white tracking-tight truncate">
                                    {loading ? 'Loading...' : (org?.name || 'Organization Statistics')}
                                </Dialog.Title>
                                {org?.acronym && (
                                    <p className="text-blue-100/80 text-sm mt-0.5">{org.acronym} &bull; {org.campus}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Scrollable body */}
                    <div className="overflow-y-auto flex-1 p-8 space-y-8">
                        {loading && (
                            <div className="flex items-center justify-center py-16">
                                <div className="w-8 h-8 border-2 border-blue-500/30 border-t-blue-500 rounded-full animate-spin" />
                            </div>
                        )}

                        {error && (
                            <div className="p-4 rounded-xl text-sm bg-red-500/10 border border-red-500/20 text-red-500">
                                {error}
                            </div>
                        )}

                        {data && (
                            <>
                                {/* Top info grid */}
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    {/* Academic Years */}
                                    <Section title="Available Academic Years" icon={Calendar}>
                                        <div className="rounded-xl border border-white/10 bg-muted/30 p-4 text-sm">
                                            {data.years?.length > 0 ? (
                                                <ul className="space-y-1">
                                                    {data.years.map(y => <li key={y} className="text-foreground">{y}</li>)}
                                                </ul>
                                            ) : (
                                                <p className="text-muted-foreground">No financial report years found.</p>
                                            )}
                                        </div>
                                    </Section>

                                    {/* Quick Links */}
                                    <Section title="Quick Links" icon={ExternalLink}>
                                        <div className="rounded-xl border border-white/10 bg-muted/30 p-4 space-y-3">
                                            <a
                                                href={`/admin/documents?organization_id=${org?.id}`}
                                                className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors"
                                            >
                                                <FileText className="w-4 h-4" />
                                                View Documents
                                            </a>
                                            {(data.cal_first_year || data.first_year) && (
                                                <p className="text-xs text-muted-foreground">Downloads use the first listed academic year by default.</p>
                                            )}
                                            <div className="flex flex-wrap gap-2">
                                                {data.cal_first_year && (
                                                    <>
                                                        <a href={`/admin/calendar-activities/download/${org?.id}/${data.cal_first_year}`}
                                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs font-medium hover:bg-white/5 transition-colors">
                                                            <Download className="w-3.5 h-3.5" /> Calendar
                                                        </a>
                                                        <a href={`/admin/calendar-activities/print/${org?.id}/${data.cal_first_year}`}
                                                            target="_blank" rel="noopener noreferrer"
                                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs font-medium hover:bg-white/5 transition-colors">
                                                            <Printer className="w-3.5 h-3.5" /> Print Calendar
                                                        </a>
                                                    </>
                                                )}
                                                {data.first_year && (
                                                    <>
                                                        <a href={`/admin/program-expenditures/download/${org?.id}/${data.first_year}`}
                                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs font-medium hover:bg-white/5 transition-colors">
                                                            <Download className="w-3.5 h-3.5" /> Expenditure
                                                        </a>
                                                        <a href={`/admin/program-expenditures/print/${org?.id}/${data.first_year}`}
                                                            target="_blank" rel="noopener noreferrer"
                                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs font-medium hover:bg-white/5 transition-colors">
                                                            <Printer className="w-3.5 h-3.5" /> Print Expenditure
                                                        </a>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    </Section>
                                </div>

                                {/* Commitment Forms */}
                                <Section title="Commitment Forms" icon={FileText}>
                                    <TableWrapper heads={['ID', 'Officer', 'Position', 'Academic Year', 'Signed Date', 'Status', 'Actions']}>
                                        {data.commitment_forms?.length > 0 ? (
                                            data.commitment_forms.map(cf => (
                                                <tr key={cf.id} className="border-b border-white/5 hover:bg-white/2 transition-colors">
                                                    <td className="px-4 py-3 text-muted-foreground">{cf.id}</td>
                                                    <td className="px-4 py-3 font-medium text-foreground">{cf.officer_name || ''}</td>
                                                    <td className="px-4 py-3 text-foreground">{cf.position || ''}</td>
                                                    <td className="px-4 py-3 text-foreground">{cf.academic_year || ''}</td>
                                                    <td className="px-4 py-3 text-muted-foreground">{cf.signed_date_formatted || ''}</td>
                                                    <td className="px-4 py-3">
                                                        <span className={cn(
                                                            'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                                            cf.status === 'submitted' ? 'bg-green-500/10 text-green-500 border border-green-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20'
                                                        )}>
                                                            {cf.status}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        <div className="flex gap-1">
                                                            <a href={`/admin/commitment-forms/download/${cf.id}`}
                                                                className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-600 text-white text-xs hover:bg-blue-700 transition-colors">
                                                                <Download className="w-3 h-3" /> Download
                                                            </a>
                                                            <a href={`/admin/commitment-forms/print/${cf.id}`}
                                                                target="_blank" rel="noopener noreferrer"
                                                                className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border border-white/10 text-xs hover:bg-white/5 transition-colors">
                                                                <Printer className="w-3 h-3" /> Print
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <EmptyRow cols={7} message="No commitment forms found." />
                                        )}
                                    </TableWrapper>
                                </Section>

                                {/* Approved Financial Reports */}
                                <Section title="Uploaded Financial Reports (Approved)" icon={TrendingUp}>
                                    <TableWrapper heads={['Academic Year', 'Title', 'Submitted By', 'Date', 'Actions']}>
                                        {data.approved_financial_report_documents?.length > 0 ? (
                                            data.approved_financial_report_documents.map((doc, i) => (
                                                <tr key={doc.id || i} className="border-b border-white/5 hover:bg-white/2 transition-colors">
                                                    <td className="px-4 py-3 text-foreground">{doc.academic_year || ''}</td>
                                                    <td className="px-4 py-3 font-medium text-foreground">{doc.document_title || ''}</td>
                                                    <td className="px-4 py-3 text-muted-foreground">{doc.submitted_by_name || ''}</td>
                                                    <td className="px-4 py-3 text-muted-foreground">{doc.created_at_formatted || ''}</td>
                                                    <td className="px-4 py-3">
                                                        {doc.id && (
                                                            <div className="flex gap-1">
                                                                <a href={`/admin/documents/view/${doc.id}`}
                                                                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-600 text-white text-xs hover:bg-blue-700 transition-colors">
                                                                    View
                                                                </a>
                                                                <a href={`/admin/documents/download/${doc.id}`}
                                                                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border border-white/10 text-xs hover:bg-white/5 transition-colors">
                                                                    <Download className="w-3 h-3" /> Download
                                                                </a>
                                                            </div>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <EmptyRow cols={5} message="No approved uploaded financial reports found." />
                                        )}
                                    </TableWrapper>
                                </Section>

                                {/* Financial Reports (Yearly) */}
                                <Section title="Financial Reports (Yearly)" icon={BarChart2}>
                                    <TableWrapper heads={['Academic Year', 'Total Collection', 'Total Expenses', 'Remaining Fund', 'Status', 'Actions']}>
                                        {data.financial_reports?.length > 0 ? (
                                            data.financial_reports.map((fr, i) => (
                                                <tr key={fr.id || i} className="border-b border-white/5 hover:bg-white/2 transition-colors">
                                                    <td className="px-4 py-3 font-medium text-foreground">{fr.academic_year || ''}</td>
                                                    <td className="px-4 py-3 text-green-400">₱ {Number(fr.total_collection || 0).toLocaleString()}</td>
                                                    <td className="px-4 py-3 text-red-400">₱ {Number(fr.total_expenses || 0).toLocaleString()}</td>
                                                    <td className="px-4 py-3 text-blue-400">₱ {Number(fr.total_remaining_fund || 0).toLocaleString()}</td>
                                                    <td className="px-4 py-3">
                                                        <span className="text-sm text-muted-foreground capitalize">{fr.status || ''}</span>
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        {fr.id && (
                                                            <div className="flex gap-1">
                                                                <a href={`/admin/financial-reports/download/${fr.id}`}
                                                                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-600 text-white text-xs hover:bg-blue-700 transition-colors">
                                                                    <Download className="w-3 h-3" /> Download
                                                                </a>
                                                                <a href={`/admin/financial-reports/print/${fr.id}`}
                                                                    target="_blank" rel="noopener noreferrer"
                                                                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border border-white/10 text-xs hover:bg-white/5 transition-colors">
                                                                    <Printer className="w-3 h-3" /> Print
                                                                </a>
                                                            </div>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <EmptyRow cols={6} message="No financial reports found." />
                                        )}
                                    </TableWrapper>
                                </Section>

                                {/* Expenditure Summary */}
                                <Section title="Program Expenditure Summary (Yearly)" icon={TrendingUp}>
                                    <TableWrapper heads={['Academic Year', 'Grand Total', 'Actions']}>
                                        {data.expenditure_summary?.length > 0 ? (
                                            data.expenditure_summary.map((row, i) => (
                                                <tr key={i} className="border-b border-white/5 hover:bg-white/2 transition-colors">
                                                    <td className="px-4 py-3 font-medium text-foreground">{row.academic_year || ''}</td>
                                                    <td className="px-4 py-3 text-blue-400">₱ {Number(row.grand_total || 0).toLocaleString()}</td>
                                                    <td className="px-4 py-3">
                                                        {org?.id && row.academic_year && (
                                                            <div className="flex gap-1">
                                                                <a href={`/admin/program-expenditures/download/${org.id}/${row.academic_year}`}
                                                                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-600 text-white text-xs hover:bg-blue-700 transition-colors">
                                                                    <Download className="w-3 h-3" /> Download
                                                                </a>
                                                                <a href={`/admin/program-expenditures/print/${org.id}/${row.academic_year}`}
                                                                    target="_blank" rel="noopener noreferrer"
                                                                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border border-white/10 text-xs hover:bg-white/5 transition-colors">
                                                                    <Printer className="w-3 h-3" /> Print
                                                                </a>
                                                            </div>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <EmptyRow cols={3} message="No expenditure summary found." />
                                        )}
                                    </TableWrapper>
                                </Section>
                            </>
                        )}
                    </div>
                </Dialog.Content>
            </Dialog.Portal>
        </Dialog.Root>
    );
};

export default OrganizationStatisticsModal;
