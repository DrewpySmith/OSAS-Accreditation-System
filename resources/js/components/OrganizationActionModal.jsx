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
    const [loading, setLoading] = useState(false);
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
        setError('');
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
            const response = await fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                onSaveSuccess(data.message);
                onOpenChange(false);
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

    return (
        <Dialog.Root open={open} onOpenChange={onOpenChange}>
            <Dialog.Portal>
                <Dialog.Overlay className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 animate-in fade-in duration-300" />
                <Dialog.Content className="fixed left-[50%] top-[50%] z-50 grid w-full max-w-2xl translate-x-[-50%] translate-y-[-50%] gap-4 border bg-card p-6 shadow-lg duration-200 animate-in fade-in zoom-in-95 sm:rounded-xl text-card-foreground">
                    <div class="flex flex-col space-y-1.5 text-left">
                        <Dialog.Title className="text-xl font-bold leading-none tracking-tight flex items-center gap-2">
                            <span class="text-blue-500"><Building2 class="w-5 h-5" /></span>
                            {isEdit ? 'Edit Organization' : 'Create New Organization'}
                        </Dialog.Title>
                        <Dialog.Description className="text-sm text-muted-foreground">
                            {isEdit ? 'Update organization details and status.' : 'Register a new student organization to the system.'}
                        </Dialog.Description>
                    </div>

                    {error && (
                        <div className="p-3 rounded-md text-sm bg-red-500/10 border border-red-500/20 text-red-500">
                            {error}
                        </div>
                    )}

                    <form onSubmit={handleSubmit} class="space-y-4 py-4">
                        <input type="hidden" name="csrf_test_name" value={document.querySelector('meta[name="X-CSRF-TOKEN"]')?.content} />

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Organization Name</label>
                                <input name="name" defaultValue={formData.name} required placeholder="Full organization name" class="flex h-10 w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Acronym</label>
                                <input name="acronym" defaultValue={formData.acronym} placeholder="e.g. USG" class="flex h-10 w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Campus</label>
                                <select name="campus" defaultValue={formData.campus} required class="flex h-10 w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">-- Select Campus --</option>
                                    {campuses.map(c => <option key={c} value={c}>{c}</option>)}
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Status</label>
                                <select name="status" defaultValue={formData.status} class="flex h-10 w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium">Description</label>
                            <textarea name="description" defaultValue={formData.description} rows={3} class="flex w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {!isEdit && (
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Username</label>
                                    <input name="username" defaultValue={formData.username} required placeholder="account_username" class="flex h-10 w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                            )}
                            <div class="space-y-2">
                                <label class="text-sm font-medium">
                                    {isEdit ? 'Reset Password (Optional)' : 'Initial Password'}
                                </label>
                                <input type="password" name="new_password" placeholder={isEdit ? "Leave blank to keep current" : "Set initial password"} required={!isEdit} class="flex h-10 w-full rounded-md border border-white/10 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4">
                            <Dialog.Close asChild>
                                <button type="button" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-white/10 bg-card hover:bg-muted h-10 px-4 py-2 mt-2 sm:mt-0 transition-colors">
                                    Cancel
                                </button>
                            </Dialog.Close>
                            <button type="submit" disabled={loading} class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 h-10 px-4 py-2 disabled:opacity-50 transition-colors">
                                <Save class="w-4 h-4 mr-2" />
                                {loading ? 'Saving...' : (isEdit ? 'Update Organization' : 'Create Organization')}
                            </button>
                        </div>
                    </form>

                    <Dialog.Close className="absolute right-4 top-4 rounded-sm opacity-70 transition-opacity hover:opacity-100 outline-none">
                        <X className="h-4 w-4" />
                        <span className="sr-only">Close</span>
                    </Dialog.Close>
                </Dialog.Content>
            </Dialog.Portal>
        </Dialog.Root>
    );
};

export default OrganizationActionModal;
