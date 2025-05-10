import React, { useRef, useState } from "react";
import { ChevronUp, ChevronDown, Menu as MenuIcon, X as XIcon, ExternalLink } from 'lucide-react';
import { Link } from "@inertiajs/react";

export default function Nav() {
  const [open, setOpen] = useState<number | null>(null);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [menu, setMenu] = React.useState<any[]>([]);
  const navRef = useRef<HTMLUListElement>(null);

  React.useEffect(() => {
    fetch('/api/navigation-items')
    .then(res => res.json())
    .then(data => setMenu(data));

    const handler = (e: KeyboardEvent) => {
      if (e.key === "Escape") setOpen(null);
    };
    window.addEventListener("keydown", handler);
    return () => window.removeEventListener("keydown", handler);
  }, []);

  const handleKeyDown = (idx: number, hasDropdown: boolean) => (
    e: React.KeyboardEvent
  ) => {
    if (!hasDropdown) return;
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      setOpen(open === idx ? null : idx);
      setTimeout(() => {
        const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
          `[data-dropdown="${idx}"] a`
        );
        dropdown?.[0]?.focus();
      }, 0);
    }
    if (e.key === "ArrowDown") {
      e.preventDefault();
      if (open !== idx) {
        setOpen(idx);
        setTimeout(() => {
          const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
            `[data-dropdown="${idx}"] a`
          );
          dropdown?.[0]?.focus();
        }, 0);
      } else {
        const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
          `[data-dropdown="${idx}"] a`
        );
        dropdown?.[0]?.focus();
      }
    }
    if (e.key === "ArrowUp") {
      e.preventDefault();
      if (open !== idx) {
        setOpen(idx);
        setTimeout(() => {
          const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
            `[data-dropdown="${idx}"] a`
          );
          dropdown?.[dropdown.length - 1]?.focus();
        }, 0);
      } else {
        const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
          `[data-dropdown="${idx}"] a`
        );
        dropdown?.[dropdown.length - 1]?.focus();
      }
    }
    if (e.key === "Tab") setOpen(null);
  };

  const handleDropdownKeyDown = (idx: number, i: number, arrLen: number) => (
    e: React.KeyboardEvent
  ) => {
    if (e.key === "ArrowDown") {
      e.preventDefault();
      const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
        `[data-dropdown="${idx}"] a`
      );
      const nextIdx = (i + 1) % (dropdown?.length || 1);
      dropdown?.[nextIdx]?.focus();
    }
    if (e.key === "ArrowUp") {
      e.preventDefault();
      const dropdown = navRef.current?.querySelectorAll<HTMLAnchorElement>(
        `[data-dropdown="${idx}"] a`
      );
      const prevIdx = (i - 1 + (dropdown?.length || 1)) % (dropdown?.length || 1);
      dropdown?.[prevIdx]?.focus();
    }
    if (e.key === "Tab") setOpen(null);
    if (e.key === "Escape") setOpen(null);
  };

  return (
    <nav
      className="w-full text-header-text bg-header-background border-t contrast:border-b border-neutral-300/20 dark:border-neutral-700/20 py-2 px-2 contrast:border-foreground/60 relative"
      aria-label="Główna nawigacja"
    >
      {/* Hamburger mobile */}
      <div className="flex items-center justify-between xl:hidden">
        <span className="font-bold text-lg">MENU</span>
        <button
          className="xl:hidden p-2"
          aria-label={mobileOpen ? "Zamknij menu" : "Otwórz menu"}
          onClick={() => setMobileOpen((open) => !open)}
        >
          {mobileOpen ? <XIcon /> : <MenuIcon />}
        </button>
      </div>
      {/* Desktop menu */}
      <ul
        className="hidden xl:flex flex-row gap-2 p-0 m-0 list-none"
        ref={navRef}
      >
        {menu.map((item, idx) => (
          <li key={item.id || idx} className="relative">
            {item.type === 'article' && item.article ? (
              <a
                href={item.article.slug}
                className="px-4 py-2 block rounded focus:outline focus:outline-2 focus:outline-neutral-300/20 dark:focus:outline-neutral-700/20 contrast:focus:outline-foreground"
                tabIndex={0}
              >
                {item.article.title}
              </a>
            ) : item.type === 'category' && item.articles && item.articles.length > 0 ? (
              <>
                <button
                  className="flex items-center justify-center px-4 py-2 block bg-transparent border-none cursor-pointer rounded focus:outline focus:outline-2 focus:outline-neutral-300/20 dark:focus:outline-neutral-700/20 contrast:focus:outline-foreground"
                  aria-haspopup="menu"
                  aria-expanded={open === idx}
                  aria-controls={`dropdown-${idx}`}
                  tabIndex={0}
                  onClick={() => setOpen(open === idx ? null : idx)}
                  onKeyDown={handleKeyDown(idx, true)}
                >
                  {item.name || 'Kategoria'}
                  <div className="ml-1">{open === idx ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}</div>
                </button>
                {open === idx && (
                  <ul
                    id={`dropdown-${idx}`}
                    data-dropdown={idx}
                    role="menu"
                    className="absolute left-0 top-full min-w-[200px] bg-neutral-100 dark:bg-neutral-100 contrast:bg-black text-black dark:text-black contrast:text-foreground contrast:border contrast:border-foreground shadow-lg z-50 mt-1 rounded"
                  >
                    {item.articles.filter((d: any) => d.active).map((d: any, i: number) => (
                      <li key={d.id}>
                        {d.external ? (
                          <a
                            href={d.slug}
                            className="flex items-center justify-between px-4 py-2 hover:bg-gray-100 contrast:focus:bg-foreground contrast:focus:text-black focus:rounded focus:outline focus:outline-2 focus:outline-neutral-700/40 dark:focus:outline-neutral-800 contrast:focus:outline-foreground hover:contrast:bg-foreground hover:contrast:text-black"
                            tabIndex={0}
                            role="menuitem"
                            onKeyDown={handleDropdownKeyDown(idx, i, item.articles.filter((d: any) => d.active).length)}
                            onClick={() => setOpen(null)}
                          >
                            {d.title}
                            <ExternalLink className="h-4 w-4 ml-1" />
                          </a>
                        ) : (
                          <Link
                            href={d.slug}
                            className="block px-4 py-2 hover:bg-gray-100 contrast:focus:bg-foreground contrast:focus:text-black focus:rounded focus:outline focus:outline-2 focus:outline-neutral-700/40 dark:focus:outline-neutral-800 contrast:focus:outline-foreground hover:contrast:bg-foreground hover:contrast:text-black"
                            tabIndex={0}
                            role="menuitem"
                            onKeyDown={handleDropdownKeyDown(idx, i, item.articles.filter((d: any) => d.active).length)}
                            onClick={() => setOpen(null)}
                          >
                            {d.title}
                          </Link>
                        )}
                      </li>
                    ))}
                  </ul>
                )}
              </>
            ) : null}
          </li>
        ))}
      </ul>

      {/* Mobile menu */}
      <ul
        className={`xl:hidden flex-col gap-2 p-0 m-0 list-none absolute left-0 w-full bg-header-background z-50 transition-all duration-200 ${mobileOpen ? "flex" : "hidden"}`}
      >
        {menu.map((item, idx) => (
          <li key={item.id || idx} className="relative border-b border-neutral-300/20 dark:border-neutral-700/20">
            {item.type === 'article' && item.article ? (
              <a
                href={item.article.slug}
                className="px-4 py-3 block w-full text-left rounded focus:outline focus:outline-2 focus:outline-neutral-300/20 dark:focus:outline-neutral-700/20 contrast:focus:outline-foreground"
                tabIndex={0}
                onClick={() => setMobileOpen(false)}
              >
                {item.article.title}
              </a>
            ) : item.type === 'category' && item.articles && item.articles.length > 0 ? (
              <>
                <button
                  className="flex items-center justify-between w-full px-4 py-3 bg-transparent border-none cursor-pointer rounded focus:outline focus:outline-2 focus:outline-neutral-300/20 dark:focus:outline-neutral-700/20 contrast:focus:outline-foreground"
                  onClick={() => setOpen(open === idx ? null : idx)}
                  tabIndex={0}
                >
                  {item.name || 'Kategoria'}
                  <div className="ml-1">{open === idx ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}</div>
                </button>
                {open === idx && (
                  <ul
                    className="w-full bg-neutral-100 dark:bg-neutral-100 contrast:bg-black text-black dark:text-black contrast:text-foreground contrast:border contrast:border-foreground shadow-lg z-50 rounded"
                  >
                    {item.articles.filter((d: any) => d.active).map((d: any, i: number) => (
                      <li key={d.id}>
                        {d.external ? (
                          <a
                            href={d.slug}
                            className="flex items-center justify-between px-4 py-3 hover:bg-gray-100 contrast:focus:bg-foreground contrast:focus:text-black focus:rounded focus:outline focus:outline-2 focus:outline-neutral-700/40 dark:focus:outline-neutral-800 contrast:focus:outline-foreground hover:contrast:bg-foreground hover:contrast:text-black"
                            tabIndex={0}
                            onClick={() => { setOpen(null); setMobileOpen(false); }}
                          >
                            {d.title}
                            <ExternalLink className="h-4 w-4 ml-1" />
                          </a>
                        ) : (
                          <Link
                            href={d.slug}
                            className="block px-4 py-3 hover:bg-gray-100 contrast:focus:bg-foreground contrast:focus:text-black focus:rounded focus:outline focus:outline-2 focus:outline-neutral-700/40 dark:focus:outline-neutral-800 contrast:focus:outline-foreground hover:contrast:bg-foreground hover:contrast:text-black"
                            tabIndex={0}
                            onClick={() => { setOpen(null); setMobileOpen(false); }}
                          >
                            {d.title}
                          </Link>
                        )}
                      </li>
                    ))}
                  </ul>
                )}
              </>
            ) : null}
          </li>
        ))}
      </ul>
    </nav>
  );
}