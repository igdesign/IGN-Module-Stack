/**
 * Development Utilities
 *
 * This file should only contain scripts useful for developement only
 * This file should not be included in the distribution build process
 */

/**
 * DEBUG
 *
 * defines DEBUG as true in development environment
 * but when used with uglify in the distribution environment, sets
 * debug to false (using uglify --define 'DEBUG'=fale)
 * This allows us to hide blocks of code used for developement
 *
 * eg: DEBUG && console.log('I won't exist in distribution code');
 */

if (typeof DEBUG === 'undefined')
{
  DEBUG = true;
}

