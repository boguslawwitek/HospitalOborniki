import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Search, X } from 'lucide-react';
import { HTMLAttributes, useState, useEffect, useRef } from 'react';
import { cn } from '@/lib/utils';
import { Link } from "@inertiajs/react";

interface NavigationArticle {
  id: number;
  title: string;
  slug: string;
  external?: boolean;
  active?: boolean;
}

interface NavigationCategory {
  id: number;
  name: string;
  type: 'category';
  articles: NavigationArticle[];
}

interface NavigationArticleItem {
  id: number;
  type: 'article';
  article: NavigationArticle;
}

type NavigationItem = NavigationCategory | NavigationArticleItem;

export function SearchInput({ className = '', ...props }: HTMLAttributes<HTMLDivElement>) {
  const [searchTerm, setSearchTerm] = useState('');
  const [navigationItems, setNavigationItems] = useState<NavigationItem[]>([]);
  const [searchResults, setSearchResults] = useState<Array<{title: string, slug: string, external?: boolean, category?: string}>>([]);
  const [isSearching, setIsSearching] = useState(false);
  const [showResults, setShowResults] = useState(false);
  const searchRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    fetch('/api/navigation-items')
      .then(res => res.json())
      .then(data => setNavigationItems(data));

    const handleClickOutside = (event: MouseEvent) => {
      if (searchRef.current && !searchRef.current.contains(event.target as Node)) {
        setShowResults(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleSearch = () => {
    if (!searchTerm.trim()) {
      setSearchResults([]);
      setShowResults(false);
      return;
    }

    setIsSearching(true);
    const results: Array<{title: string, slug: string, external?: boolean, category?: string}> = [];
    const searchTermLower = searchTerm.toLowerCase();
    
    const matchingCategories = navigationItems.filter(
      item => item.type === 'category' && item.name.toLowerCase().includes(searchTermLower)
    );
    
    matchingCategories.forEach(category => {
      if (category.type === 'category' && category.articles) {
        category.articles.forEach(article => {
          if (article.active !== false) {
            results.push({
              title: article.title,
              slug: article.slug,
              external: article.external,
              category: category.name
            });
          }
        });
      }
    });
    
    navigationItems.forEach(item => {
      if (item.type === 'article' && item.article) {
        if (item.article.title.toLowerCase().includes(searchTermLower)) {
          const exists = results.some(r => r.slug === item.article.slug);
          if (!exists) {
            results.push({
              title: item.article.title,
              slug: item.article.slug,
              external: item.article.external
            });
          }
        }
      } else if (item.type === 'category' && item.articles) {
        if (!matchingCategories.includes(item)) {
          item.articles.forEach(article => {
            if (article.active !== false && article.title.toLowerCase().includes(searchTermLower)) {
              const exists = results.some(r => r.slug === article.slug);
              if (!exists) {
                results.push({
                  title: article.title,
                  slug: article.slug,
                  external: article.external,
                  category: item.name
                });
              }
            }
          });
        }
      }
    });

    setSearchResults(results);
    setShowResults(true);
    setIsSearching(false);
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      handleSearch();
    }
  };

  const clearSearch = () => {
    setSearchTerm('');
    setSearchResults([]);
    setShowResults(false);
    inputRef.current?.focus();
  };

  return (
    <div className={cn('relative w-full max-w-sm select-none', className)} {...props} ref={searchRef}>
      <div className="flex items-center space-x-2">
        <div className="relative flex-1">
          <Input 
            type="search" 
            className="border-transparent bg-neutral-100 dark:bg-neutral-800 contrast:bg-primary text-black dark:text-white contrast:text-black contrast:placeholder-gray-700 h-full pr-8" 
            placeholder="Wyszukaj..." 
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            onKeyDown={handleKeyDown}
            onFocus={() => searchTerm && setShowResults(true)}
            ref={inputRef}
          />
          {searchTerm && (
            <button 
              className="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
              onClick={clearSearch}
              aria-label="Wyczyść wyszukiwanie"
            >
              <X className="h-4 w-4" />
            </button>
          )}
        </div>
        <Button
          type="submit"
          aria-label="Szukaj"
          className="bg-red-700 hover:bg-red-700/60 dark:bg-red-800 dark:hover:bg-red-700 contrast:bg-primary hover:contrast:bg-primary/60 text-white dark:text-white contrast:text-black h-full flex items-center justify-center" 
          onClick={handleSearch}
          disabled={isSearching}
        >
          <Search className="h-5 w-5 font-bold" />
        </Button>
      </div>

      {showResults && (
        <div className="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-neutral-800 contrast:bg-foreground shadow-lg rounded-md z-50 max-h-[300px] overflow-y-auto">
          {searchResults.length > 0 ? (
            <div className="py-2">
              {(() => {
                const groupedResults: Record<string, typeof searchResults> = {};
                
                searchResults.forEach(result => {
                  if (result.category) {
                    if (!groupedResults[result.category]) {
                      groupedResults[result.category] = [];
                    }
                    groupedResults[result.category].push(result);
                  }
                });
                
                const uncategorized = searchResults.filter(result => !result.category);
                if (uncategorized.length > 0) {
                  groupedResults['Inne'] = uncategorized;
                }
                
                return Object.entries(groupedResults).map(([category, results], groupIndex) => (
                  <div key={groupIndex} className="mb-2">
                    <div className="px-4 py-1 font-semibold text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-neutral-900 contrast:bg-black contrast:text-black contrast:bg-foreground">
                      {category}
                    </div>
                    <ul>
                      {results.map((result, index) => (
                        <li key={index} className="px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 contrast:hover:bg-black">
                          {result.external ? (
                            <a 
                              href={result.slug} 
                              className="block w-full text-black dark:text-white contrast:hover:text-foreground flex items-center"
                              target="_blank"
                              rel="noopener noreferrer"
                              onClick={() => setShowResults(false)}
                            >
                              {result.title}
                            </a>
                          ) : (
                            <Link 
                              href={result.slug} 
                              className="block w-full text-black dark:text-white contrast:hover:text-foreground"
                              onClick={() => setShowResults(false)}
                            >
                              {result.title}
                            </Link>
                          )}
                        </li>
                      ))}
                    </ul>
                  </div>
                ));
              })()}
            </div>
          ) : (
            <div className="p-4 text-center text-gray-500 dark:text-gray-400">
              Nie znaleziono wyników dla {searchTerm}
            </div>
          )}
        </div>
      )}
    </div>
  )
}
