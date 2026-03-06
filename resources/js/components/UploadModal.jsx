import React, { useState, useEffect } from 'react';
import * as Dialog from '@radix-ui/react-dialog';
import { X, Upload, FileText, CheckCircle2 } from 'lucide-react';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

const UploadModal = ({ documentTypes }) => {
    const [open, setOpen] = useState(false);
    const [formData, setFormData] = useState({
        document_type: '',
        document_title: '',
        academic_year: new Date().getFullYear() + '-' + (new Date().getFullYear() + 1),
        description: ''
    });
    const [file, setFile] = useState(null);
    const [loading, setLoading] = useState(false);
    const [status, setStatus] = useState({ type: '', message: '' });

    // Listen for the global custom event to open the modal
    useEffect(() => {
        const handleOpen = () => setOpen(true);
        const btn = document.getElementById('quick-upload-btn');
        if (btn) btn.addEventListener('click', handleOpen);
        return () => {
            if (btn) btn.removeEventListener('click', handleOpen);
        };
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setStatus({ type: '', message: '' });

        const formDataToSend = new FormData(e.currentTarget);

        try {
            const response = await fetch(e.currentTarget.action, {
                method: 'POST',
                body: formDataToSend,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                setStatus({ type: 'success', message: data.message });
                // Reset form on success after a short delay
                setTimeout(() => {
                    setOpen(false);
                    window.location.reload(); // Refresh to show new submission
                }, 1500);
            } else {
                const errorMsg = data.errors ? Object.values(data.errors).join(', ') : (data.message || 'Upload failed');
                setStatus({ type: 'error', message: errorMsg });
            }
        } catch (error) {
            setStatus({ type: 'error', message: 'An unexpected error occurred.' });
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog.Root open={open} onOpenChange={setOpen}>
            <Dialog.Portal>
                <Dialog.Overlay className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 animate-in fade-in duration-300" />
                <Dialog.Content className="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-background p-6 shadow-lg duration-200 animate-in fade-in zoom-in-95 sm:rounded-xl">
                    <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                        <Dialog.Title className="text-xl font-semibold leading-none tracking-tight flex items-center gap-2">
                            <span class="text-secondary">📤</span> Upload Document
                        </Dialog.Title>
                        <Dialog.Description className="text-sm text-muted-foreground">
                            Submit your documents for accreditation review.
                        </Dialog.Description>
                    </div>

                    <form onSubmit={handleSubmit} action="/organization/submissions/upload" method="POST" enctype="multipart/form-data" class="space-y-4 py-4">
                        {status.message && (
                            <div className={cn(
                                "p-3 rounded-md text-sm mb-4 border",
                                status.type === 'success' ? "bg-green-500/10 border-green-500/20 text-green-500" : "bg-red-500/10 border-red-500/20 text-red-500"
                            )}>
                                {status.message}
                            </div>
                        )}

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Document Type</label>
                            <select name="document_type" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="">-- Select --</option>
                                {Object.entries(documentTypes).map(([val, label]) => (
                                    <option key={val} value={val}>{label}</option>
                                ))}
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Document Title</label>
                            <input name="document_title" placeholder="e.g. Financial Report Q1" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium leading-none">Academic Year</label>
                                <input name="academic_year" defaultValue={formData.academic_year} required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium leading-none">File</label>
                                <input type="file" name="document_file" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Description (Optional)</label>
                            <textarea name="description" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4">
                            <Dialog.Close asChild>
                                <button type="button" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 mt-2 sm:mt-0">
                                    Cancel
                                </button>
                            </Dialog.Close>
                            <button
                                type="submit"
                                disabled={loading}
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-green-600 text-white hover:bg-green-700 h-10 px-4 py-2 disabled:opacity-50"
                            >
                                {loading ? 'Uploading...' : 'Upload Document'}
                            </button>
                        </div>
                    </form>

                    <Dialog.Close className="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-accent data-[state=open]:text-muted-foreground">
                        <X className="h-4 w-4" />
                        <span className="sr-only">Close</span>
                    </Dialog.Close>
                </Dialog.Content>
            </Dialog.Portal>
        </Dialog.Root>
    );
};

export default UploadModal;
