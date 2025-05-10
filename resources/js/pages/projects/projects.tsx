import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';

interface ProjectType {
  id: number;
  title: string;
  slug: string;
  created_at: string;
  updated_at: string;
}

interface Project {
  id: number;
  title: string;
  slug: string;
  body: string;
  logo: string;
  active: boolean;
  published_at: string;
  created_at: string;
  updated_at: string;
  user_id: number;
  projectTypes: ProjectType[];
}

interface ProjectsApiResponse {
  projects: Project[];
  projectType: ProjectType | null;
}

interface ProjectsPageProps {
  typeSlug?: string;
}

export default function Projects({ typeSlug }: ProjectsPageProps) {
  const [projectsData, setProjectsData] = useState<ProjectsApiResponse | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    const url = `/api/projects/${encodeURIComponent(typeSlug || '')}`;
      
    fetch(url)
      .then(response => response.json())
      .then(data => {
        setProjectsData(data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Error fetching projects:', error);
        setProjectsData(null);
        setLoading(false);
      });
  }, [typeSlug]);

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Strona Główna',
      href: '/',
    },
    {
      title: 'Projekty',
      href: '#',
      disabled: true,
    },
    {
      title: projectsData?.projectType?.title || 'Projekty',
      href: typeSlug ? `/projekty/${typeSlug}` : '/projekty',
    },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`${projectsData?.projectType?.title || 'Projekty'}`} />
      <div>
        <main className="bg-background max-w-screen-xl mx-auto px-4 xl:px-0 pt-8 pb-12">
          
          <h1 className="text-3xl font-bold mb-6">{projectsData?.projectType?.title || 'Projekty'}</h1>
          
          {projectsData?.projectType === null && projectsData?.projects && projectsData.projects.length > 0 && (
            <div className="flex flex-wrap gap-2 mb-6">
              <Link
                href="/projekty"
                className={`px-3 py-1 text-sm rounded-full ${!typeSlug ? 'bg-primary text-primary-foreground' : 'bg-muted hover:bg-muted/80'}`}
              >
                Wszystkie
              </Link>
              {Array.from(new Set(
                projectsData.projects.flatMap(project => 
                  project.projectTypes.map(type => JSON.stringify(type))
                )
              )).map(typeString => {
                const type = JSON.parse(typeString) as ProjectType;
                return (
                  <Link
                    key={type.id}
                    href={`/projekty/${type.slug}`}
                    className={`px-3 py-1 text-sm rounded-full ${typeSlug === type.slug ? 'bg-primary text-primary-foreground' : 'bg-muted hover:bg-muted/80'}`}
                  >
                    {type.title}
                  </Link>
                );
              })}
            </div>
          )}

          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="text-lg">Wczytywanie...</div>
            </div>
          ) : !projectsData || !projectsData.projects || projectsData.projects.length === 0 ? (
            <div className="flex justify-center items-center h-64">
              <div className="text-lg">Brak projektów.</div>
            </div>
          ) : (
            <div>
              <div className="space-y-8 mb-8">
                {projectsData.projects.map((project) => {
                  function stripHtml(html: string) {
                    const htmlWithSpaces = html
                      .replace(/<br\s*\/?>/gi, ' ')
                      .replace(/<\/p><p>/gi, ' ')
                      .replace(/<\/div><div>/gi, ' ')
                      .replace(/<\/h[1-6]><h[1-6]>/gi, ' ')
                      .replace(/<\/li><li>/gi, ' ');
                        
                    const div = document.createElement('div');
                    div.innerHTML = htmlWithSpaces;
                    
                    return (div.textContent || div.innerText || '')
                      .replace(/\s+/g, ' ')
                      .trim();
                  }
                  const plainText = stripHtml(project.body);
                  const shortText = plainText.length > 200 
                    ? plainText.slice(0, 200).replace(/\s+$/, '') + '…' 
                    : plainText;
                  
                  return (
                    <div key={project.id} className="p-6 bg-card rounded-lg shadow-sm">
                      {project.logo && (
                        <div className="flex justify-center mb-4">
                          <img 
                            src={project.logo} 
                            alt={project.title} 
                            className="max-h-24 object-contain"
                          />
                        </div>
                      )}
                      <h2 className="text-2xl font-bold mb-2">{project.title}</h2>
                      
                      {project.projectTypes && project.projectTypes.length > 0 && (
                        <div className="flex flex-wrap gap-1 mb-3">
                          {project.projectTypes.map(type => (
                            <Link
                              key={type.id}
                              href={`/projekty/${type.slug}`}
                              className="px-2 py-0.5 text-xs bg-muted rounded-full hover:bg-muted/80"
                            >
                              {type.title}
                            </Link>
                          ))}
                        </div>
                      )}
                      
                      <div className="text-foreground mb-4">{shortText}</div>
                      <Link
                        href={`/projekty/${project.projectTypes && project.projectTypes.length > 0 ? project.projectTypes[0].slug : (typeSlug || '')}/${project.slug}`}
                        className="inline-block text-primary hover:underline font-semibold"
                      >
                        Czytaj więcej
                      </Link>
                    </div>
                  );
                })}
              </div>
            </div>
          )}
        </main>
      </div>
    </AppLayout>
  );
}
