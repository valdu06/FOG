/*
 *  GSI-SSHTools - Java SSH2 API
 *
 *  Copyright (C) 2005-6 CCLRC.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Library General Public License
 *  as published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 *
 *  You may also distribute it and/or modify it under the terms of the
 *  Apache style J2SSH Software License. A copy of which should have
 *  been provided with the distribution.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  License document supplied with your distribution for more details.
 *
 */

package com.sshtools.j2ssh.authentication;

import com.sshtools.j2ssh.io.*;
import java.io.*;
import java.util.Properties;
import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.globus.common.CoGProperties;
import org.globus.gsi.*;
import org.globus.gsi.gssapi.*;
import org.globus.gsi.gssapi.auth.*;
import org.globus.myproxy.MyProxy;
import org.globus.tools.ProxyInit;
import org.globus.tools.proxy.DefaultGridProxyModel;
import org.globus.util.Util;
import org.gridforum.jgss.ExtendedGSSContext;
import org.gridforum.jgss.ExtendedGSSCredential;
import org.ietf.jgss.*;

public class EKEYXAuthenticationClient extends SshAuthenticationClient
{

    public EKEYXAuthenticationClient()
    {
    }

    public final String getMethodName()
    {
        return "external-keyx";
    }

    int attempt = 0;

    public void reset()
    {
	attempt = 0;	
    }

    public void authenticate(AuthenticationProtocolClient authenticationprotocolclient, String s)
        throws IOException, TerminatedStateException, IllegalArgumentException
    {
        try
        {
	    if(attempt++!=0) throw new IllegalArgumentException();
            GSSContext gsscontext = authenticationprotocolclient.getGSSContext();
	    if(gsscontext==null) throw new IllegalArgumentException();
            log.debug("Creating GSS context base on grid credentials.");



	    /* --- MIC calculated over: 
	       string      session identifier
	       byte        SSH_MSG_USERAUTH_REQUEST
	       string      user name
	       string      service
	       string      "gssapi-keyex"
	    */
	    ByteArrayWriter baw = new ByteArrayWriter();
	    baw.writeBinaryString(authenticationprotocolclient.getSessionIdentifier()); // session identifier?!?!?
	    baw.write(50);
	    baw.writeString(getUsername());
	    baw.writeString(s);
	    baw.writeString("external-keyx");

            log.debug("Sending external-keyx user auth request.");
	    byte mic[] = gsscontext.getMIC(baw.toByteArray(), 0, baw.toByteArray().length, null) ;


	    /*
	      byte        SSH_MSG_USERAUTH_REQUEST
	      string      user name
	      string      service
	      string      "gssapi-keyex"
	      string      MIC
	    */

            SshMsgUserAuthRequest sshmsguserauthrequest = new SshMsgUserAuthRequest(getUsername(), s, "external-keyx", null);
            authenticationprotocolclient.sendMessage(sshmsguserauthrequest);
            log.debug("Receiving user auth response.");
            //SshMsgUserauthGssapiResponse sshmsguserauthgssapiresponse = (SshMsgUserauthGssapiResponse)authenticationprotocolclient.readMessage(52);
          
            //log.debug("Authentication exchange complete, Context information:");
            //log.debug("Initiator : " + gsscontext.getSrcName());
            //log.debug("Acceptor  : " + gsscontext.getTargName());
            //log.debug("Lifetime  : " + gsscontext.getLifetime());
            //log.debug("Privacy   : " + gsscontext.getConfState());
            //log.debug("Anonymity : " + gsscontext.getAnonymityState());
        }
        catch(GSSException gssexception)
        {
            gssexception.printStackTrace();
            StringWriter stringwriter = new StringWriter();
            gssexception.printStackTrace(new PrintWriter(stringwriter));
            log.debug(stringwriter);
        }
	
    }


    public Properties getPersistableProperties()
    {
        Properties properties = new Properties();
        return properties;
    }

    public void setPersistableProperties(Properties properties)
    {
    }

    public boolean canAuthenticate()
    {
        return true;
    }


    private static Log log;

    static 
    {
        log = LogFactory.getLog(com.sshtools.j2ssh.authentication.EKEYXAuthenticationClient.class);
    }
}
