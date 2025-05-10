import { type BreadcrumbItem } from '@/types';
import { type ReactNode } from 'react';
import Header from '@/components/header/header';
import Nav from '@/components/nav/nav';
import Footer from '@/components/footer/footer';
import { Breadcrumbs } from '@/components/breadcrumbs';

interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => (
    <>
        <Header {...props} />
        <Nav />
        {breadcrumbs && <Breadcrumbs breadcrumbs={breadcrumbs} />}
        {children}
        <Footer />
    </>
);