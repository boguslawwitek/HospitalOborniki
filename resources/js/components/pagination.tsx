import React from 'react';
import {
    Pagination as UIPagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';

interface PaginationProps {
    currentPage: number;
    lastPage: number;
    onPageChange: (page: number) => void;
    className?: string;
}

export function Pagination({ currentPage, lastPage, onPageChange, className = 'my-8' }: PaginationProps) {
    return (
        <UIPagination className={className}>
            <PaginationContent>
                {currentPage > 1 && (
                    <PaginationItem>
                        <PaginationPrevious 
                            href="#" 
                            onClick={(e) => {
                                e.preventDefault();
                                onPageChange(Math.max(currentPage - 1, 1));
                            }} 
                        />
                    </PaginationItem>
                )}
                
                {/* First page */}
                <PaginationItem>
                    <PaginationLink 
                        href="#" 
                        isActive={currentPage === 1}
                        onClick={(e) => {
                            e.preventDefault();
                            onPageChange(1);
                        }}
                    >
                        1
                    </PaginationLink>
                </PaginationItem>
                
                {/* Ellipsis if needed */}
                {currentPage > 3 && (
                    <PaginationItem>
                        <PaginationEllipsis />
                    </PaginationItem>
                )}
                
                {/* Page before current if not first or second page */}
                {currentPage > 2 && (
                    <PaginationItem>
                        <PaginationLink 
                            href="#" 
                            onClick={(e) => {
                                e.preventDefault();
                                onPageChange(currentPage - 1);
                            }}
                        >
                            {currentPage - 1}
                        </PaginationLink>
                    </PaginationItem>
                )}
                
                {/* Current page if not first page */}
                {currentPage !== 1 && currentPage !== lastPage && (
                    <PaginationItem>
                        <PaginationLink 
                            href="#" 
                            isActive={true}
                            onClick={(e) => e.preventDefault()}
                        >
                            {currentPage}
                        </PaginationLink>
                    </PaginationItem>
                )}
                
                {/* Page after current if not last or second-to-last page */}
                {currentPage < lastPage - 1 && (
                    <PaginationItem>
                        <PaginationLink 
                            href="#" 
                            onClick={(e) => {
                                e.preventDefault();
                                onPageChange(currentPage + 1);
                            }}
                        >
                            {currentPage + 1}
                        </PaginationLink>
                    </PaginationItem>
                )}
                
                {/* Ellipsis if needed */}
                {currentPage < lastPage - 2 && (
                    <PaginationItem>
                        <PaginationEllipsis />
                    </PaginationItem>
                )}
                
                {/* Last page if not first page */}
                {lastPage > 1 && (
                    <PaginationItem>
                        <PaginationLink 
                            href="#" 
                            isActive={currentPage === lastPage}
                            onClick={(e) => {
                                e.preventDefault();
                                onPageChange(lastPage);
                            }}
                        >
                            {lastPage}
                        </PaginationLink>
                    </PaginationItem>
                )}
                
                {currentPage < lastPage && (
                    <PaginationItem>
                        <PaginationNext 
                            href="#" 
                            onClick={(e) => {
                                e.preventDefault();
                                onPageChange(Math.min(currentPage + 1, lastPage));
                            }} 
                        />
                    </PaginationItem>
                )}
            </PaginationContent>
        </UIPagination>
    );
}
