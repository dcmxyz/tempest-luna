/**
 * Lightweight typed URI helper. Builds URLs from the generated Tempest route manifest.
 * Run `internal:typescript-routes` (or let the Vite plugin do it) to keep routes in sync.
 *
 * uri('/login')                                   -> "/login"
 * uri('/test/{abc}', { abc: 1 })                  -> "/test/1"
 * uri('/test/{abc}', { abc: 1, page: 2 })         -> "/test/1?page=2"
 * uri('/test/{abc}/{?xyz}', { abc: 1 })            -> "/test/1"
 *
 * uriIs('/users/*')                               -> true  (on /users/1)
 * uriIs('/login')                                 -> true  (on /login)
 * uriIs('/users/*', '/users/1')                   -> true
 * uriIs('/login', '/dashboard')                   -> false
 */

import { routes } from '@tempest/typescript/routes';

// { method: "GET", uri: "/login" } | { method: "POST", uri: "/login" } | ...
type AppRoute = typeof routes[number];

// "/" | "/login" | "/test/{abc}" | "/test/{abc}/{?xyz}" | ...
export type Uri = AppRoute['uri'];

// "GET" | "POST" | ...
export type Method = AppRoute['method'];

// "/test/{abc}/{?id}" -> { abc: string | number, id?: string | number }
// | "/{abc}/{?id}" -> { abc: string | number, id?: string | number }
// | ...
type ExtractParams<T extends string> = T extends `${string}{${infer Param}}${infer Rest}`
        ? Param extends `?${infer Optional}`
            ? { [K in Optional]?: string | number } & ExtractParams<Rest>
            : { [K in Param]: string | number } & ExtractParams<Rest>
        : {};

// Alias for ExtractParams<U>
type UriParams<U extends Uri> = ExtractParams<U>;

// Has any params?
type HasParams<U extends Uri> = keyof UriParams<U> extends never ? false : true;

// Resolves a URI by replacing route params and appending unknown keys as query string.
//
// uri('/test/{abc}', { abc: 1, page: 2 })      -> "/test/1?page=2"
// | uri('/test/{abc}/{xyz}', { abc: 1, xyz: 2})  -> "/test/1/2"
// | uri('/test/{abc}/{?xyz}', { abc: 1 })        -> "/test/1"
// | uri('/login')                                -> "/login"
//
// If the URI has route params, the second argument is required and must include all required params (not ?).
// Extra keys beyond the route params are appended as query string.
// If the URI has no route params, the second argument is optional (query string only).
export function uri<U extends Uri>(
    path: U,
    ...args: HasParams<U> extends true
        ? [params: UriParams<U> & Record<string, string | number>]
        : [params?: Record<string, string | number>]
): string {
    const params = args[0] as Record<string, string | number> | undefined;

    if (!params) return path;

    // Extract param names from the URI: "/test/{abc}/{?id}" -> Set { "abc", "id" }
    const paramPattern = /\{(\??\w+)(?::[^}]*)?\}/g;
    const matches = Array.from(path.matchAll(paramPattern));
    const routeParams = new Set(matches.map(m => m[1].replace('?', '')));

    const pathParams: Record<string, string | number> = {};
    const queryParams: Record<string, string | number> = {};

    // Keys matching a route param -> substituted into the path
    // Everything else -> appended as ?key=value
    for (const [key, value] of Object.entries(params)) {
        if (routeParams.has(key)) {
            pathParams[key] = value;
        } else {
            queryParams[key] = value;
        }
    }

    // Replace {param} and {?param} params with their values
    const resolvedPath = Object.entries(pathParams).reduce<string>(
        (r, [key, value]) => r
            .replace(`{${key}}`, String(value))
            .replace(`{?${key}}`, String(value)),
        path
    );

    // Build query string
    const queryString = new URLSearchParams(
        Object.entries(queryParams)
            .map(([k, v]) => [k, String(v)])
    ).toString();

    return queryString
        ? `${resolvedPath}?${queryString}`
        : resolvedPath;
}

export function uriIs(path: Uri | (string & {}), current: string = window.location.pathname): boolean {
    if (path.endsWith('/*')) {
        const base = path.slice(0, -2);
        return current.startsWith(`${base}/`);
    }

    if (path.endsWith('*')) {
        const base = path.slice(0, -1);
        return current.startsWith(base);
    }

    return current === path;
}