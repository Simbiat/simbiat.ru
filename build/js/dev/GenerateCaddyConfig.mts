/**
 * @file Script to generate Caddy config.
 * Ran in the console, so console output is allowed.
 * Email is one used for ACME, not a PII.
 */

/* eslint-disable no-console */

import hosts from '../../docker/frankenphp/config/hosts.jsonc';
import logs from '../../docker/frankenphp/config/logs.jsonc';
import crowdsec from '../../docker/frankenphp/config/crowdsec.jsonc';
import https from '../../docker/frankenphp/config/https.jsonc';
import hsts from '../../docker/frankenphp/config/hsts.jsonc';
import headers_remove from '../../docker/frankenphp/config/headers_remove.jsonc';
import headers_add from '../../docker/frankenphp/config/headers_add.jsonc';
import headers_deny from '../../docker/frankenphp/config/headers_deny.jsonc';
import headers_access_control from '../../docker/frankenphp/config/headers_access_control.jsonc';
import headers_cache_control from '../../docker/frankenphp/config/headers_cache_control.jsonc';
import headers_path_based from '../../docker/frankenphp/config/headers_path_based.jsonc';
import headers_cross_origin from '../../docker/frankenphp/config/headers_cross_origin.jsonc';
import headers_vary from '../../docker/frankenphp/config/headers_vary.jsonc';
import headers_utf from '../../docker/frankenphp/config/headers_utf.jsonc';
import headers_compression from '../../docker/frankenphp/config/headers_compression.jsonc';
import internal_redirects from '../../docker/frankenphp/config/internal_redirects.jsonc';
import domain_redirects from '../../docker/frankenphp/config/domain_redirects.jsonc';
import rewrites from '../../docker/frankenphp/config/rewrites.jsonc';
import php from '../../docker/frankenphp/config/php.jsonc';
import hotlinks from '../../docker/frankenphp/config/hotlinks.jsonc';
import rate_limit from '../../docker/frankenphp/config/rate_limit.jsonc';

// The following 3 interfaces are just to help with suppressing type-related errors
interface HostMatch {
  host: string[]
}

interface StaticResponseHandler {
  handler: 'static_response'
  headers: {
    'Location': string[]
    'X-Redirect-Source': string[]
  }
  status_code: number
  close: boolean
}

interface DomainRedirect {
  match: HostMatch[]
  handle: StaticResponseHandler[]
}

/**
 * Actual generator.
 */
async function main(): Promise<void> {
  try {
    const output_file = './config/caddy.json';
    const all_hosts = [
      ...((hosts as Record<string, unknown>)['alt'] as []),
      ...((hosts as Record<string, unknown>)['supops'] as []),
      ...((hosts as Record<string, unknown>)['main'] as []),
    ];
    const domain_redirects_raw = domain_redirects as DomainRedirect[];
    if (domain_redirects_raw[0]?.match?.[0]) {
      domain_redirects_raw[0].match[0].host = [
        ...((hosts as Record<string, unknown>)['alt'] as []),
      ];
    }
    if (domain_redirects_raw[1]?.match?.[0]) {
      domain_redirects_raw[1].match[0].host = [
        ...((hosts as Record<string, unknown>)['supops'] as []),
      ];
    }
    // Routes used by both normal and error processing.
    const common_routes = [
      // Common headers manipulation
      {
        handle: [
          {
            ...headers_remove,
          },
          {
            ...headers_add,
          },
          {
            ...headers_deny,
          },
          ...(headers_access_control as []),
          ...(headers_cache_control as []),
          //Set Allow, if empty
          {
            handler: 'headers',
            response: {
              set: {
                Allow: [
                  'HEAD, OPTIONS, GET',
                ],
              },
              require: {
                headers: {
                  Allow: null,
                },
              },
            },
          },
          {
            ...headers_path_based,
          },
          {
            ...headers_cross_origin,
          },
          ...(headers_vary as []),
          {
            ...headers_utf,
          },
          {
            ...headers_compression,
          },
        ],
      },
      {
        ...https,
      },
      {
        ...hsts,
      },
      ...domain_redirects_raw,
    ];

    // Caddy's config tree.
    const caddy_config = {
      logging: {
        logs: logs as Record<string, unknown>,
      },
      apps: {
        crowdsec: crowdsec as Record<string, unknown>,
        tls: {
          certificates: {
            automate: [
              ...all_hosts,
            ],
          },
          automation: {
            policies: [
              {
                subjects: [
                  ...all_hosts,
                ],
                issuers: [
                  //Let's Encrypt is default
                  {
                    module: 'acme',
                    ca: 'https://acme-v02.api.letsencrypt.org/directory',
                    email: 'letsencrypt@simbiat.eu',
                    acme_timeout: '60s',
                  },
                  //Internal is supposed to be used only for localhost
                  {
                    module: 'internal',
                    lifetime: '30d',
                  },
                ],
                key_type: 'rsa4096',
                must_staple: false,
              },
            ],
          },
        },
        frankenphp: {},
        http: {
          grace_period: '30s',
          shutdown_delay: '10s',
          servers: {
            server_0: {
              listen: [
                ':80',
                ':443',
              ],
              //Enable access logs for Crowdsec
              logs: {
                default_logger_name: 'access_log',
              },
              read_timeout: '10s',
              read_header_timeout: '10s',
              write_timeout: '30m',
              strict_sni_host: true,
              routes: [
                {
                  handle: [
                    rate_limit,
                  ],
                },
                // Crowdsec
                {
                  handle: [
                    {
                      handler: 'crowdsec',
                    },
                  ],
                },
                // Coraza provided by Crowdsec
                {
                  handle: [
                    {
                      handler: 'appsec',
                    },
                  ],
                },
                ...(common_routes as []),
                // Main hosts' normal processing.
                {
                  match: [
                    {
                      host: [
                        ...((hosts as Record<string, unknown>)['main'] as []),
                      ],
                    },
                  ],
                  handle: [
                    {
                      ...internal_redirects,
                    },
                    {
                      ...rewrites,
                    },
                    {
                      handler: 'subroute',
                      routes: [
                        ...(php as []),
                      ],
                    },
                  ],
                  terminal: true,
                },
              ],
              // Error processing.
              errors: {
                routes: [
                  ...(common_routes as []),
                  {
                    handle: [
                      {
                        ...internal_redirects,
                      },
                      // Placing this here to prevent hotlinking, but to allow loading resources from the custom page.
                      // "Sec-Fetch-Site: none" covers direct navigation.
                      // "Sec-Fetch-Dest: document" covers opening resources in a new tab from the current document.
                      {
                        handler: 'subroute',
                        routes: [
                          hotlinks,
                        ],
                      },
                      {
                        ...rewrites,
                      },
                      {
                        handler: 'subroute',
                        routes: [
                          {
                            handle: [
                              {
                                handler: 'vars',
                                root: '/app/public',
                              },
                            ],
                          },
                          ...(php as []),
                        ],
                      },
                    ],
                    terminal: true,
                  },
                ],
              },
            },
          },
        },
      },
    };

    await Bun.write(output_file, JSON.stringify(caddy_config, null, 2));
    console.log(`✅ Caddy config written to: ${output_file}`);
  } catch (err) {
    throw new Error(err instanceof Error ? err.message : '', { cause: err });
  }
}

void (async (): Promise<void> => {
  try {
    await main();
  } catch (err) {
    console.error(`❌ Config generation failed: ${err instanceof Error ? err.message : String(err)}`);
    process.exit(1);
  }
})();
