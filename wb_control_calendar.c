/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Calendar control

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"
#include <time.h>

//---------------------------------------------------------- FUNCTION PROTOTYPES

// Private

static DWORD FileTimeToUnixTime(FILETIME *filetime);
static FILETIME UnixTimeToFileTime(DWORD dwTime);
static LONG GetCorrectUtcUnixTime(void);

//----------------------------------------------------------- EXPORTED FUNCTIONS

time_t GetCalendarTime(PWBOBJ pwbo)
{
	time_t UnixTime;
	PSYSTEMTIME lpSysTime;
	LPFILETIME ptrFileTime;

	lpSysTime = wbMalloc(sizeof(SYSTEMTIME));
	ptrFileTime = wbMalloc(sizeof(FILETIME));

	SendMessage(pwbo->hwnd, MCM_GETCURSEL, 0, (LPARAM)lpSysTime);
	if(!lpSysTime)
		return FALSE;

	lpSysTime->wHour = 0;
	lpSysTime->wMinute = 0;
	lpSysTime->wSecond = 0;
	lpSysTime->wMilliseconds = 0;
	SystemTimeToFileTime(lpSysTime,ptrFileTime);
	UnixTime = FileTimeToUnixTime(ptrFileTime);
	UnixTime += 60 * GetCorrectUtcUnixTime();
	return UnixTime;
}

BOOL SetCalendarTime(PWBOBJ pwbo, time_t UnixTime)
{
	FILETIME fileTime;
	LPSYSTEMTIME ptrSystemTime;

	ptrSystemTime = wbMalloc(sizeof(SYSTEMTIME));

	UnixTime -= 60 * GetCorrectUtcUnixTime();
	fileTime = UnixTimeToFileTime(UnixTime);
	FileTimeToSystemTime(&fileTime,ptrSystemTime);
	SendMessage(pwbo->hwnd, MCM_SETCURSEL, 0, (LPARAM)ptrSystemTime);
	return TRUE;
}


//------------------------------------------------------------ PRIVATE FUNCTIONS

/*

No assumptions are made about DST here. We take information from the TIME_ZONE_INFORMATION
structure to correct UnixTime to UTC.

*/

static LONG GetCorrectUtcUnixTime(void)
{
	DWORD InfoTimeResult;
	LPTIME_ZONE_INFORMATION ptrTimeZoneInfo;
	LONG lBias;

	ptrTimeZoneInfo = wbMalloc(sizeof(TIME_ZONE_INFORMATION));
	InfoTimeResult = GetTimeZoneInformation(ptrTimeZoneInfo);
	lBias = ptrTimeZoneInfo->Bias ;
	if(InfoTimeResult == TIME_ZONE_ID_DAYLIGHT)
		lBias += ptrTimeZoneInfo->DaylightBias;

	return lBias;
}

/*

 Adapted from http://www.cocoadev.com/index.pl?ConvertNSDateToWIndowsFILETIME
 Original code from the Microsoft web site

*/

static DWORD FileTimeToUnixTime(FILETIME *pfiletime)
{
	long long int t;

	t = pfiletime->dwHighDateTime;
    t <<= 32;
    t += (unsigned long)pfiletime->dwLowDateTime;
    t -= 116444736000000000LL;

	return (DWORD)(t / 10000000);
}

/*

 Adapted from http://www.cocoadev.com/index.pl?ConvertNSDateToWIndowsFILETIME
 Original code from the Microsoft web site

*/

static FILETIME UnixTimeToFileTime(DWORD dwTime)
{
	FILETIME retf;
	long long int ll;

	ll = ((long long)dwTime * 10000000LL);
	ll += 116444736000000000LL;
	retf.dwHighDateTime = (DWORD)(ll >> 32);
	retf.dwLowDateTime = (DWORD)(ll & 0x00000000FFFFFFFF);

	return retf;
}

//------------------------------------------------------------------ END OF FILE
