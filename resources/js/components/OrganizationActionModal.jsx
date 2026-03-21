import React, { useState, useEffect } from 'react';
import * as Dialog from '@radix-ui/react-dialog';
import { X, Building2, Save } from 'lucide-react';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

const OrganizationActionModal = ({ open, onOpenChange, record, campuses, onSaveSuccess }) => {
    const isEdit = !!record;
    const [step, setStep] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const [error, setError] = useState('');
    const [formData, setFormData] = useState({
        name: '',
        acronym: '',
        campus: '',
        description: '',
        status: 'active',
        username: '',
        new_password: ''
    });

    useEffect(() => {
        if (open) {
            if (record) {
                setFormData({
                    name: record.name || '',
                    acronym: record.acronym || '',
                    campus: record.campus || '',
                    description: record.description || '',
                    status: record.status || 'active',
                    username: record.username || '',
                    new_password: ''
                });
            } else {
                setFormData({
                    name: '',
                    acronym: '',
                    campus: '',
                    description: '',
                    status: 'active',
                    username: '',
                    new_password: ''
                });
            }
            setStep(1);
            setIsSuccess(false);
            setError('');
        }
    }, [record, open]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        const url = isEdit
            ? `/admin/organizations/update/${record.id}`
            : '/admin/organizations/store';

        try {
            const fd = new FormData(e.currentTarget);
            const csrfHeaderName = document.querySelector('meta[name="csrf_header"]')?.content || 'X-CSRF-TOKEN';
            const csrfHash = document.querySelector('meta[name="csrf_hash"]')?.content || document.querySelector('meta[name="X-CSRF-TOKEN"]')?.content;
            
            const response = await fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeaderName]: csrfHash
                }
            });

            const data = await response.json();
            if (data.success) {
                setIsSuccess(true);
                setTimeout(() => {
                    onSaveSuccess(data.message);
                    onOpenChange(false);
                }, 1500);
            } else {
                const errorMsg = data.errors ? Object.values(data.errors).join(', ') : (data.message || 'Operation failed');
                setError(errorMsg);
            }
        } catch (err) {
            setError('An unexpected error occurred.');
        } finally {
            setLoading(false);
        }
    };

    const nextStep = () => setStep(2);
    const prevStep = () => setStep(1);

    return (
        <Dialog.Root open={open} onOpenChange={onOpenChange}>
            <Dialog.Portal>
                <Dialog.Overlay className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 animate-in fade-in duration-300" />
                <Dialog.Content className="fixed left-[50%] top-[50%] z-50 w-full max-w-2xl bg-card rounded-3xl shadow-2xl overflow-hidden border border-white/10 text-card-foreground animate-modalSlideIn focus:outline-none">
                    
                    {/* Success State Overlay */}
                    {isSuccess && (
                        <div className="absolute inset-0 bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center z-50 animate-fadeIn">
                            <div className="text-center space-y-4">
                                <div className="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full animate-scaleIn shadow-xl">
                                    <svg className="w-12 h-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h3 className="text-3xl font-bold text-white">Saving Changes...</h3>
                                <p className="text-blue-100">Updating system records</p>
                            </div>
                        </div>
                    )}

                    {/* Header */}
                    <div className="relative bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8">
                        <Dialog.Close className="absolute top-6 right-6 text-white/80 hover:text-white transition-colors outline-none">
                            <X className="w-6 h-6" />
                        </Dialog.Close>
                        <div className="flex items-center gap-3">
                            <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                                <Building2 className="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <Dialog.Title className="text-2xl font-bold text-white tracking-tight">
                                    {isEdit ? 'Update Organization' : 'Create Organization'}
                                </Dialog.Title>
                                <p className="text-blue-100/80 text-sm mt-0.5">
                                    {isEdit ? 'Modify existing system records' : 'Register a new student organization'}
                                </p>
                            </div>
                        </div>

                        {/* Progress Indicator */}
                        <div className="flex items-center gap-2 mt-8">
                            {[1, 2].map((s) => (
                                <div key={s} className={cn(
                                    "flex-1 h-1.5 rounded-full transition-all duration-500",
                                    s <= step ? "bg-white shadow-[0_0_10px_rgba(255,255,255,0.5)]" : "bg-white/20"
                                )} />
                            ))}
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="p-8 relative min-h-[400px]">
                        <input type="hidden" name="csrf_osas_token" value={document.querySelector('meta[name="X-CSRF-TOKEN"]')?.content} />

                        {error && (
                            <div className="p-4 rounded-xl text-sm bg-red-500/10 border border-red-500/20 text-red-500 mb-6 animate-fadeIn">
                                {error}
                            </div>
                        )}

                        {/* Step 1: Account & Personnel */}
                        <div className={cn(
                            "space-y-6 transition-all duration-500",
                            step === 1 ? "opacity-100 translate-x-0" : "opacity-0 translate-x-12 absolute inset-x-8 pointer-events-none"
                        )}>
                            <div className="grid grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-sm font-semibold text-muted-foreground ml-1">Organization Name</label>
                                    <input name="name" defaultValue={formData.name} required placeholder="Full Name" className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-semibold text-muted-foreground ml-1">Acronym</label>
                                    <input name="acronym" defaultValue={formData.acronym} placeholder="e.g. USG" className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-6">
                                {!isEdit && (
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-muted-foreground ml-1">Username</label>
                                        <input name="username" defaultValue={formData.username} required placeholder="account_username" className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
                                    </div>
                                )}
                                <div className="space-y-2">
                                    <label className="text-sm font-semibold text-muted-foreground ml-1">
                                        {isEdit ? 'Update Password' : 'Initial Password'}
                                    </label>
                                    <input type="password" name="new_password" placeholder={isEdit ? "Optional" : "Required"} required={!isEdit} className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
                                </div>
                            </div>

                            <div className="flex justify-end pt-6">
                                <button type="button" onClick={nextStep} className="group flex items-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/20 transition-all active:scale-95">
                                    Next Details
                                    <svg className="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {/* Step 2: Details */}
                        <div className={cn(
                            "space-y-6 transition-all duration-500",
                            step === 2 ? "opacity-100 translate-x-0" : "opacity-0 -translate-x-12 absolute inset-x-8 pointer-events-none"
                        )}>
                            <div className="grid grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-sm font-semibold text-muted-foreground ml-1">Campus</label>
                                    <select name="campus" defaultValue={formData.campus} required className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                                        <option value="">-- Select Campus --</option>
                                        {campuses.map(c => <option key={c} value={c}>{c}</option>)}
                                    </select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-semibold text-muted-foreground ml-1">Status</label>
                                    <select name="status" defaultValue={formData.status} className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-semibold text-muted-foreground ml-1">Description</label>
                                <textarea name="description" defaultValue={formData.description} rows={4} className="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none" placeholder="Provide a brief background of the organization..." />
                            </div>

                            <div className="flex items-center gap-4 pt-6">
                                <button type="button" onClick={prevStep} className="flex items-center gap-2 px-6 py-3 bg-muted hover:bg-muted/80 text-muted-foreground rounded-xl font-bold transition-all active:scale-95">
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Back
                                </button>
                                <button type="submit" disabled={loading} className="flex-1 flex items-center justify-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/20 transition-all disabled:opacity-50 active:scale-95">
                                    {loading ? (
                                        <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                    ) : (
                                        <Save className="w-5 h-5" />
                                    )}
                                    {loading ? 'Processing...' : (isEdit ? 'Update Organization' : 'Create Organization')}
                                </button>
                            </div>
                        </div>
                    </form>
                </Dialog.Content>
            </Dialog.Portal>
        </Dialog.Root>
    );
};

export default OrganizationActionModal;
