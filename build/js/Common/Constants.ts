import { getSearchParam } from 'Common/Url.ts';

export const TIMEZONE = Intl.DateTimeFormat()
                            .resolvedOptions().timeZone;
export const AJAX_TIMEOUT = 60000;
export const SNACKBAR_FAIL_LIFE = 10000;
export const ACCESS_TOKEN = getSearchParam('access_token');
