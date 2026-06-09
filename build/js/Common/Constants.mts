/**
 * @file Constants used in multiple places.
 */
import { getSearchParam } from './Helpers.mts';
import { TimeManager } from './TimeManager.mts';

export const TIMEZONE = Intl.DateTimeFormat()
                            .resolvedOptions().timeZone;
export const ACCESS_TOKEN = getSearchParam('access_token');
export const HTTP_NOT_FOUND = 404;
export const TIME_MANAGER = new TimeManager();
