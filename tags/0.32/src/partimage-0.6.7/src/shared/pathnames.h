#ifndef _PATHNAMES_H_
#define _PATHNAMES_H_

#define PARTIMAGE_LOG "/dev/null/partimage-debug.log"
#define PARTIMAGED_LOG "/dev/null/partimaged.log"

// partimaged will change euid to this user
#define PARTIMAGED_USER "partimag"

#ifdef DEVEL_SUPPORT
  // define if you want to append pid number to PARTIMAGE_LOG
  // ex: /var/log/partimage-debug.log_45121 instead of 
  // /var/log/partimage-debug.log
  // this can be used when debugging with several running partimage
  #define APPEND_PID 1

  #define DEFAULT_DEBUG_LEVEL 5
#else // DEVEL_SUPPORT
  #define DEFAULT_DEBUG_LEVEL 1
#endif // DEVEL_SUPPORT

#define PARTIMAGED_USERS "/usr/local/etc/partimaged/partimagedusers"

// used by SSL.
#define CERTF "/usr/local/etc/partimaged/partimaged.cert"
#define KEYF "/usr/local/etc/partimaged/partimaged.key"

// you can use CInterfaceNewt 
#define PARTIMAGE_INTERFACE CInterfaceNewt

#endif // _PATHNAMES_H_
