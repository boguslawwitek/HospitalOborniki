import React from 'react';

export interface Attachment {
    id: number;
    file_name: string;
    file_path: string;
}

interface AttachmentsProps {
    attachments: Attachment[];
    title?: string;
    className?: string;
}

export default function AttachmentsList({ 
    attachments, 
    title = "Załączniki", 
    className = "" 
}: AttachmentsProps) {
    if (!attachments || attachments.length === 0) {
        return null;
    }

    return (
        <div className={`mt-8 w-fit ${className}`}>
            <h2 className="text-2xl font-semibold mb-4">{title}</h2>
            <div className="flex flex-col gap-2">
                {attachments.map(attachment => (
                    <a 
                        key={attachment.id} 
                        href={attachment.file_path} 
                        target="_blank" 
                        rel="noopener noreferrer"
                        className="flex items-center w-fit p-3 pl-3 pr-6 bg-muted rounded-md hover:bg-muted/80 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <span>{attachment.file_name}</span>
                    </a>
                ))}
            </div>
        </div>
    );
};
