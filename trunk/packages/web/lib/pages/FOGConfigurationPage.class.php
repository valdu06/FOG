<?php

// Blackout - 9:51 AM 23/02/2012
class FOGConfigurationPage extends FOGPage
{
	// Base variables
	var $name = 'FOG Configuration';
	var $node = 'about';
	var $id = 'id';
	
	// Menu Items
	var $menu = array(
		
	);
	var $subMenu = array(
		
	);
	
	// Pages
	public function index()
	{
		
	}
	
	// Version
	public function version()
	{
		// Set title
		$this->title = _('FOG Version Information');

		echo ( "<p>" );
		echo ( "&nbsp;"._("Version").": " . FOG_VERSION  );
		echo ( "</p>" );
		echo ( "<p>" );
		echo ( "<div class=\"sub\">" );
		
		print $this->FOGCore->FetchURL("http://freeghost.sourceforge.net/version/index.php?version=" . FOG_VERSION);
		
		echo ( "</div>" );
		echo ( "</p>" );
	}
	
	// Licence
	public function license()
	{
		// Set title
		$this->title = _('FOG License Information');

		?>
		<pre>
	GNU GENERAL PUBLIC LICENSE
		 
Version 3, 29 June 2007

Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>

Everyone is permitted to copy and distribute verbatim copies of this license document, but changing it is not 
allowed.

Preamble

The GNU General Public License is a free, copyleft license for software and other kinds of works.

The licenses for most software and other practical works are designed to take away your freedom to share and change the works. By contrast, the GNU General Public License is intended to guarantee your freedom to share and change all versions of a program--to make sure it remains free software for all its users. We, the Free Software Foundation, use the GNU General Public License for most of our software; it applies also to any other work released this way by its authors. You can apply it to your programs, too.

When we speak of free software, we are referring to freedom, not price. Our General Public Licenses are designed to make sure that you have the freedom to distribute copies of free software (and charge for them if you wish), that you receive source code or can get it if you want it, that you can change the software or use pieces of it in new free programs, and that you know you can do these things.

To protect your rights, we need to prevent others from denying you these rights or asking you to surrender the rights. Therefore, you have certain responsibilities if you distribute copies of the software, or if you modify it: responsibilities to respect the freedom of others.

For example, if you distribute copies of such a program, whether gratis or for a fee, you must pass on to the recipients the same freedoms that you received. You must make sure that they, too, receive or can get the source code. And you must show them these terms so they know their rights.

Developers that use the GNU GPL protect your rights with two steps: (1) assert copyright on the software, and (2) offer you this License giving you legal permission to copy, distribute and/or modify it.

For the developers' and authors' protection, the GPL clearly explains that there is no warranty for this free software. For both users' and authors' sake, the GPL requires that modified versions be marked as changed, so that their problems will not be attributed erroneously to authors of previous versions.

Some devices are designed to deny users access to install or run modified versions of the software inside them, although the manufacturer can do so. This is fundamentally incompatible with the aim of protecting users' freedom to change the software. The systematic pattern of such abuse occurs in the area of products for individuals to use, which is precisely where it is most unacceptable. Therefore, we have designed this version of the GPL to prohibit the practice for those products. If such problems arise substantially in other domains, we stand ready to extend this provision to those domains in future versions of the GPL, as needed to protect the freedom of users.

Finally, every program is threatened constantly by software patents. States should not allow patents to restrict development and use of software on general-purpose computers, but in those that do, we wish to avoid the special danger that patents applied to a free program could make it effectively proprietary. To prevent this, the GPL assures that patents cannot be used to render the program non-free.

The precise terms and conditions for copying, distribution and modification follow.
TERMS AND CONDITIONS
0. Definitions.

&#x201c;This License&#x201d; refers to version 3 of the GNU General Public License.

&#x201c;Copyright&#x201d; also means copyright-like laws that apply to other kinds of works, such as semiconductor masks.

&#x201c;The Program&#x201d; refers to any copyrightable work licensed under this License. Each licensee is addressed as &#x201c;you&#x201d;. &#x201c;Licensees&#x201d; and &#x201c;recipients&#x201d; may be individuals or organizations.

To &#x201c;modify&#x201d; a work means to copy from or adapt all or part of the work in a fashion requiring copyright permission, other than the making of an exact copy. The resulting work is called a &#x201c;modified version&#x201d; of the earlier work or a work &#x201c;based on&#x201d; the earlier work.

A &#x201c;covered work&#x201d; means either the unmodified Program or a work based on the Program.

To &#x201c;propagate&#x201d; a work means to do anything with it that, without permission, would make you directly or secondarily liable for infringement under applicable copyright law, except executing it on a computer or modifying a private copy. Propagation includes copying, distribution (with or without modification), making available to the public, and in some countries other activities as well.

To &#x201c;convey&#x201d; a work means any kind of propagation that enables other parties to make or receive copies. Mere interaction with a user through a computer network, with no transfer of a copy, is not conveying.

An interactive user interface displays &#x201c;Appropriate Legal Notices&#x201d; to the extent that it includes a convenient and prominently visible feature that (1) displays an appropriate copyright notice, and (2) tells the user that there is no warranty for the work (except to the extent that warranties are provided), that licensees may convey the work under this License, and how to view a copy of this License. If the interface presents a list of user commands or options, such as a menu, a prominent item in the list meets this criterion.

1. Source Code.

The &#x201c;source code&#x201d; for a work means the preferred form of the work for making modifications to it. &#x201c;Object code&#x201d; 
means any non-source form of a work.

A &#x201c;Standard Interface&#x201d; means an interface that either is an official standard defined by a recognized standards body, or, in the case of interfaces specified for a particular programming language, one that is widely used among developers working in that language.

The &#x201c;System Libraries&#x201d; of an executable work include anything, other than the work as a whole, that (a) is included in the normal form of packaging a Major Component, but which is not part of that Major Component, and (b) serves only to enable use of the work with that Major Component, or to implement a Standard Interface for which an implementation is available to the public in source code form. A &#x201c;Major Component&#x201d;, in this context, means a major essential component (kernel, window system, and so on) of the specific operating system (if any) on which the executable work runs, or a compiler used to produce the work, or an object code interpreter used to run it.

The &#x201c;Corresponding Source&#x201d; for a work in object code form means all the source code needed to generate, install, and (for an executable work) run the object code and to modify the work, including scripts to control those activities. However, it does not include the work's System Libraries, or general-purpose tools or generally available free programs which are used unmodified in performing those activities but which are not part of the work. For example, Corresponding Source includes interface definition files associated with source files for the work, and the source code for shared libraries and dynamically linked subprograms that the work is specifically designed to require, such as by intimate data communication or control flow between those subprograms and other parts of the work.

The Corresponding Source need not include anything that users can regenerate automatically from other parts of the Corresponding Source.

The Corresponding Source for a work in source code form is that same work.
2. Basic Permissions.

All rights granted under this License are granted for the term of copyright on the Program, and are irrevocable provided the stated conditions are met. This License explicitly affirms your unlimited permission to run the unmodified Program. The output from running a covered work is covered by this License only if the output, given its content, constitutes a covered work. This License acknowledges your rights of fair use or other equivalent, as provided by copyright law.

You may make, run and propagate covered works that you do not convey, without conditions so long as your license otherwise remains in force. You may convey covered works to others for the sole purpose of having them make modifications exclusively for you, or provide you with facilities for running those works, provided that you comply with the terms of this License in conveying all material for which you do not control copyright. Those thus making or running the covered works for you must do so exclusively on your behalf, under your direction and control, on terms that prohibit them from making any copies of your copyrighted material outside their relationship with you.

Conveying under any other circumstances is permitted solely under the conditions stated below. Sublicensing is not allowed; section 10 makes it unnecessary.

3. Protecting Users' Legal Rights From Anti-Circumvention Law.

No covered work shall be deemed part of an effective technological measure under any applicable law fulfilling obligations under article 11 of the WIPO copyright treaty adopted on 20 December 1996, or similar laws prohibiting or restricting circumvention of such measures.

When you convey a covered work, you waive any legal power to forbid circumvention of technological measures to the extent such circumvention is effected by exercising rights under this License with respect to the covered work, and you disclaim any intention to limit operation or modification of the work as a means of enforcing, against the work's users, your or third parties' legal rights to forbid circumvention of technological measures. 4. Conveying Verbatim Copies.

You may convey verbatim copies of the Program's source code as you receive it, in any medium, provided that you conspicuously and appropriately publish on each copy an appropriate copyright notice; keep intact all notices stating that this License and any non-permissive terms added in accord with section 7 apply to the code; keep intact all notices of the absence of any warranty; and give all recipients a copy of this License along with the Program.

You may charge any price or no price for each copy that you convey, and you may offer support or warranty protection for a fee.
5. Conveying Modified Source Versions.

You may convey a work based on the Program, or the modifications to produce it from the Program, in the form of source code under the terms of section 4, provided that you also meet all of these conditions:

 * a) The work must carry prominent notices stating that you modified it, and giving a relevant date.
 * b) The work must carry prominent notices stating that it is released under this License and any conditions added under section 7. This requirement modifies the requirement in section 4 to &#x201c;keep intact all notices&#x201d;.
 * c) You must license the entire work, as a whole, under this License to anyone who comes into possession of a copy. This License will therefore apply, along with any applicable section 7 additional terms, to the whole of the work, and all its parts, regardless of how they are packaged. This License gives no permission to license the work in any other way, but it does not invalidate such permission if you have separately received it.
 * d) If the work has interactive user interfaces, each must display Appropriate Legal Notices; however, if the Program has interactive interfaces that do not display Appropriate Legal Notices, your work need not make them do so.

A compilation of a covered work with other separate and independent works, which are not by their nature extensions of the covered work, and which are not combined with it such as to form a larger program, in or on a volume of a storage or distribution medium, is called an &#x201c;aggregate&#x201d; if the compilation and its resulting copyright are not used to limit the access or legal rights of the compilation's users beyond what the individual works permit. Inclusion of a covered work in an aggregate does not cause this License to apply to the other parts of the aggregate.

6. Conveying Non-Source Forms.

You may convey a covered work in object code form under the terms of sections 4 and 5, provided that you also convey t he machine-readable Corresponding Source under the terms of this License, in one of these ways:

 * a) Convey the object code in, or embodied in, a physical product (including a physical distribution medium), accompanied by the Corresponding Source fixed on a durable physical medium customarily used for software interchange.
 * b) Convey the object code in, or embodied in, a physical product (including a physical distribution medium), accompanied by a written offer, valid for at least three years and valid for as long as you offer spare parts or customer support for that product model, to give anyone who possesses the object code either (1) a copy of the Corresponding Source for all the software in the product that is covered by this License, on a durable physical medium customarily used for software interchange, for a price no more than your reasonable cost of physically performing this conveying of source, or (2) access to copy the Corresponding Source from a network server at no charge.
 * c) Convey individual copies of the object code with a copy of the written offer to provide the Corresponding Source. This alternative is allowed only occasionally and noncommercially, and only if you received the object code with such an offer, in accord with subsection 6b.
 * d) Convey the object code by offering access from a designated place (gratis or for a charge), and offer equivalent access to the Corresponding Source in the same way through the same place at no further charge. You need not require recipients to copy the Corresponding Source along with the object code. If the place to copy the object code is a network server, the Corresponding Source may be on a different server (operated by you or a third party) that supports equivalent copying facilities, provided you maintain clear directions next to the object code saying where to find the Corresponding Source. Regardless of what server hosts the Corresponding Source, you remain obligated to ensure that it is available for as long as needed to satisfy these requirements.
 * e) Convey the object code using peer-to-peer transmission, provided you inform other peers where the object code and Corresponding Source of the work are being offered to the general public at no charge under subsection 6d.

A separable portion of the object code, whose source code is excluded from the Corresponding Source as a System Library, need not be included in conveying the object code work.

A &#x201c;User Product&#x201d; is either (1) a &#x201c;consumer product&#x201d;, which means any tangible personal property which is normally used for personal, family, or household purposes, or (2) anything designed or sold for incorporation into a dwelling. In determining whether a product is a consumer product, doubtful cases shall be resolved in favor of coverage. For a particular product received by a particular user, &#x201c;normally used&#x201d; refers to a typical or common use of that class of product, regardless of the status of the particular user or of the way in which the particular user actually uses, or expects or is expected to use, the product. A product is a consumer product regardless of whether the product has substantial commercial, industrial or non-consumer uses, unless such uses represent the only significant mode of use of the product.

&#x201c;Installation Information&#x201d; for a User Product means any methods, procedures, authorization keys, or other information required to install and execute modified versions of a covered work in that User Product from a modified version of its Corresponding Source. The information must suffice to ensure that the continued functioning of the modified object code is in no case prevented or interfered with solely because modification has been made.

If you convey an object code work under this section in, or with, or specifically for use in, a User Product, and the conveying occurs as part of a transaction in which the right of possession and use of the User Product is transferred to the recipient in perpetuity or for a fixed term (regardless of how the transaction is characterized), the Corresponding Source conveyed under this section must be accompanied by the Installation Information. But this requirement does not apply if neither you nor any third party retains the ability to install modified object code on the User Product (for example, the work has been installed in ROM).

The requirement to provide Installation Information does not include a requirement to continue to provide support service, warranty, or updates for a work that has been modified or installed by the recipient, or for the User Product in which it has been modified or installed. Access to a network may be denied when the modification itself materially and adversely affects the operation of the network or violates the rules and protocols for communication across the network.

Corresponding Source conveyed, and Installation Information provided, in accord with this section must be in a format that is publicly documented (and with an implementation available to the public in source code form), and must require no special password or key for unpacking, reading or copying. 

7. Additional Terms.

&#x201c;Additional permissions&#x201d; are terms that supplement the terms of this License by making exceptions from one or more of its conditions. Additional permissions that are applicable to the entire Program shall be treated as though they were included in this License, to the extent that they are valid under applicable law. If additional permissions apply only to part of the Program, that part may be used separately under those permissions, but the entire Program remains governed by this License without regard to the additional permissions.

When you convey a copy of a covered work, you may at your option remove any additional permissions from that copy, or from any part of it. (Additional permissions may be written to require their own removal in certain cases when you modify the work.) You may place additional permissions on material, added by you to a covered work, for which you have or can give appropriate copyright permission.

Notwithstanding any other provision of this License, for material you add to a covered work, you may (if authorized by the copyright holders of that material) supplement the terms of this License with terms:

 * a) Disclaiming warranty or limiting liability differently from the terms of sections 15 and 16 of this License; or
 * b) Requiring preservation of specified reasonable legal notices or author attributions in that material or in the Appropriate Legal Notices displayed by works containing it; or
 * c) Prohibiting misrepresentation of the origin of that material, or requiring that modified versions of such material be marked in reasonable ways as different from the original version; or
 * d) Limiting the use for publicity purposes of names of licensors or authors of the material; or
 * e) Declining to grant rights under trademark law for use of some trade names, trademarks, or service marks; or
 * f) Requiring indemnification of licensors and authors of that material by anyone who conveys the material (or modified versions of it) with contractual assumptions of liability to the recipient, for any liability that these contractual assumptions directly impose on those licensors and authors.

All other non-permissive additional terms are considered &#x201c;further restrictions&#x201d; within the meaning of section 10. If the Program as you received it, or any part of it, contains a notice stating that it is governed by this License along with a term that is a further restriction, you may remove that term. If a license document contains a further restriction but permits relicensing or conveying under this License, you may add to a covered work material governed by the terms of that license document, provided that the further restriction does not survive such relicensing or conveying.

If you add terms to a covered work in accord with this section, you must place, in the relevant source files, a statement of the additional terms that apply to those files, or a notice indicating where to find the applicable terms.

Additional terms, permissive or non-permissive, may be stated in the form of a separately written license, or stated as exceptions; the above requirements apply either way.

8. Termination.

You may not propagate or modify a covered work except as expressly provided under this License. Any attempt otherwise to propagate or modify it is void, and will automatically terminate your rights under this License (including any patent licenses granted under the third paragraph of section 11).

However, if you cease all violation of this License, then your license from a particular copyright holder is reinstated (a) provisionally, unless and until the copyright holder explicitly and finally terminates your license, and (b) permanently, if the copyright holder fails to notify you of the violation by some reasonable means prior to 60 days after the cessation.

Moreover, your license from a particular copyright holder is reinstated permanently if the copyright holder notifies you of the violation by some reasonable means, this is the first time you have received notice of violation of this License (for any work) from that copyright holder, and you cure the violation prior to 30 days after your receipt of the notice.

Termination of your rights under this section does not terminate the licenses of parties who have received copies or rights from you under this License. If your rights have been terminated and not permanently reinstated, you do not qualify to receive new licenses for the same material under 
section 10.

9. Acceptance Not Required for Having Copies.

You are not required to accept this License in order to receive or run a copy of the Program. Ancillary propagation of a covered work occurring solely as a consequence of using peer-to-peer transmission to receive a copy likewise does not require acceptance. However, nothing other than this License grants you permission to propagate or modify any covered work. These actions infringe copyright if you do not accept this License. Therefore, by modifying or propagating a covered work, you indicate your acceptance of this License to do so.

10. Automatic Licensing of Downstream Recipients.

Each time you convey a covered work, the recipient automatically receives a license from the original licensors, to run, modify and propagate that work, subject to this License. You are not responsible for enforcing compliance by third parties with this License.

An &#x201c;entity transaction&#x201d; is a transaction transferring control of an organization, or substantially all assets of one, or subdividing an organization, or merging organizations. If propagation of a covered work results from an entity transaction, each party to that transaction who receives a copy of the work also receives whatever licenses to the work the party's predecessor in interest had or could give under the previous paragraph, plus a right to possession of the Corresponding Source of the work from the predecessor in interest, if the predecessor has it or can get it with reasonable efforts.

You may not impose any further restrictions on the exercise of the rights granted or affirmed under this License. For example, you may not impose a license fee, royalty, or other charge for exercise of rights granted under this License, and you may not initiate litigation (including a cross-claim or counterclaim in a lawsuit) alleging that any patent claim is infringed by making, using, selling, offering for sale, or importing the Program or any portion of it.

11. Patents.

A &#x201c;contributor&#x201d; is a copyright holder who authorizes use under this License of the Program or a work on which the Program is based. The work thus licensed is called the contributor's &#x201c;contributor version&#x201d;.

A contributor's &#x201c;essential patent claims&#x201d; are all patent claims owned or controlled by the contributor, whether already acquired or hereafter acquired, that would be infringed by some manner, permitted by this License, of making, using, or selling its contributor version, but do not include claims that would be infringed only as a consequence of further modification of the contributor version. For purposes of this definition, &#x201c;control&#x201d; includes the right to grant patent sublicenses in a manner consistent with the requirements of this License.

Each contributor grants you a non-exclusive, worldwide, royalty-free patent license under the contributor's essential patent claims, to make, use, sell, offer for sale, import and otherwise run, modify and propagate the contents of its contributor version.

In the following three paragraphs, a &#x201c;patent license&#x201d; is any express agreement or commitment, however denominated, not to enforce a patent (such as an express permission to practice a patent or covenant not to sue for patent infringement). To &#x201c;grant&#x201d; such a patent license to a party means to make such an agreement or commitment not to enforce a patent against the party.

If you convey a covered work, knowingly relying on a patent license, and the Corresponding Source of the work is not available for anyone to copy, free of charge and under the terms of this License, through a publicly available network server or other readily accessible means, then you must either (1) cause the Corresponding Source to be so available, or (2) arrange to deprive yourself of the benefit of the patent license for this particular work, or (3) arrange, in a manner consistent with the requirements of this License, to extend the patent license to downstream recipients. &#x201c;Knowingly relying&#x201d; means you have actual knowledge that, but for the patent license, your conveying the covered work in a country, or your recipient's use of the covered work in a country, would infringe one or more identifiable patents in that country that you have reason to believe are valid.

If, pursuant to or in connection with a single transaction or arrangement, you convey, or propagate by procuring conveyance of, a covered work, and grant a patent license to some of the parties receiving the covered work authorizing them to use, propagate, modify or convey a specific copy of the covered work, then the patent license you grant is automatically extended to all recipients of the covered work and works based on it.

A patent license is &#x201c;discriminatory&#x201d; if it does not include within the scope of its coverage, prohibits the exercise of, or is conditioned on the non-exercise of one or more of the rights that are specifically granted under this License. You may not convey a covered work if you are a party to an arrangement with a third party that is in the business of distributing software, under which you make payment to the third party based on the extent of your activity of conveying the work, and under which the third party grants, to any of the parties who would receive the covered work from you, a discriminatory patent license (a) in connection with copies of the covered work conveyed by you (or copies made from those copies), or (b) primarily for and in connection with specific products or compilations that contain the covered work, unless you entered into that arrangement, or that patent license was granted, prior to 28 March 2007.

Nothing in this License shall be construed as excluding or limiting any implied license or other defenses to infringement that may otherwise be available to you under applicable patent law.

12. No Surrender of Others' Freedom.

If conditions are imposed on you (whether by court order, agreement or otherwise) that contradict the conditions of this License, they do not excuse you from the conditions of this License. If you cannot convey a covered work so as to satisfy simultaneously your obligations under this License and any other pertinent obligations, then as a consequence you may not convey it at all. For example, if you agree to terms that obligate you to collect a royalty for further conveying from those to whom you convey the Program, the only way you could satisfy both those terms and this License would be to refrain entirely from conveying the Program.

13. Use with the GNU Affero General Public License.

Notwithstanding any other provision of this License, you have permission to link or combine any covered work with a work licensed under version 3 of the GNU Affero General Public License into a single combined work, and to convey the resulting work. The terms of this License will continue to apply to the part which is the covered work, but the special requirements of the GNU Affero General Public License, section 13, concerning interaction through a network will apply to the combination as such.

14. Revised Versions of this License.

The Free Software Foundation may publish revised and/or new versions of the GNU General Public License from time to time. Such new versions will be similar in spirit to the present version, but may differ in detail to address new problems or concerns.

Each version is given a distinguishing version number. If the Program specifies that a certain numbered version of the GNU General Public License &#x201c;or any later version&#x201d; applies to it, you have the option of following the terms and conditions either of that numbered version or of any later version published by the Free Software Foundation. If the Program does not specify a version number of the GNU General Public License, you may choose any version ever published by the Free Software Foundation.

If the Program specifies that a proxy can decide which future versions of the GNU General Public License can be used, that proxy's public statement of acceptance of a version permanently authorizes you to choose that version for the Program.

Later license versions may give you additional or different permissions. However, no additional obligations are imposed on any author or copyright holder as a result of your choosing to follow a later version.

15. Disclaimer of Warranty.

THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM &#x201c;AS IS&#x201d; WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.

16. Limitation of Liability.

IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.

17. Interpretation of Sections 15 and 16.

If the disclaimer of warranty and limitation of liability provided above cannot be given local legal effect according to their terms, reviewing courts shall apply local law that most closely approximates an absolute waiver of all civil liability in connection with the Program, unless a warranty or assumption of liability accompanies a copy of the Program in return for a fee.

END OF TERMS AND CONDITIONS
		</pre>
		<?php
	}
	
	// Kernel Update
	public function kernel_update()
	{
		echo ( "<div class=\"hostgroup\">" );	
			echo ( _("This section allows you to update the Linux kernel which is used to boot the client computers.  In FOG, this kernel holds all the drivers for the client computer, so if you are unable to boot a client you may wish to update to a newer kernel which may have more drivers built in.  This installation process may take a few minutes, as FOG will attempt to go out to the internet to get the requested Kernel, so if it seems like the process is hanging please be patient.") );
		echo ( "</div>" );
		
		echo ( "<div>" );
		
		print $this->FOGCore->FetchURL("http://freeghost.sourceforge.net/kernelupdates/index.php?version=" . FOG_VERSION);
		
		echo ( "</div>" );	
	}
	
	// Kernel Update POST
	public function kernel_update_post()
	{
		if ( $_GET["install"] == "1"  )
		{
			$_SESSION["allow_ajax_kdl"] = true;
			$_SESSION["dest-kernel-file"] = trim($_POST["dstName"]);
			$_SESSION["tmp-kernel-file"] = rtrim(sys_get_temp_dir(), '/') . '/' . basename( $_SESSION["dest-kernel-file"] );
			$_SESSION["dl-kernel-file"] = base64_decode( $_GET["file"] );
			
			if ( file_exists( $_SESSION["tmp-kernel-file"] ) )
				@unlink( $_SESSION["tmp-kernel-file"] );
			
			?>
				<div id="kdlRes">
					<p id="currentdlstate"><?php echo(_("Starting process...")); ?></p>
					<img id='img' src="./images/loader.gif" />
				</div>
			<?php
		}
		else
		{
			echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&install=1&file=$_GET[file]\"><p>" );
				echo ( _("What would you like to name your new kernel").":  <input class=\"smaller\" type=\"text\" name=\"dstName\" value=\""._("bzImage")."\" />" );
			echo ( "</p>" );
			echo ( "<p>" );
			echo ( "<input class=\"smaller\" type=\"submit\" value=\""._("Next")."\" />" );
			echo ( "</p></form>" );
		}
	}
	
	// PXE Menu
	public function pxemenu()
	{
		// Set title
		$this->title = _("FOG PXE Boot Menu Configuration");
		
		echo ( "<form method=\"post\" action=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "\">" );
			echo ( "<p class=\"titleBottomLeft\">Boot Menu Builder</p>" );
			echo ( "<table width=\"80%\" cellpadding=\"0\" cellspacing=\"0\">" );
				echo ( "<tr>" );
					echo ( "<td>"._("Boot Menu Type: ")."</td>" );
					   echo ( "<td><select name=\"menutype\" size=\"1\" onchange=\"disableTextModePXEMenu(this);\"><option value=\"1\" label=\"Graphical\" selected=\"selected\" >"._("Graphical")."</option><option value=\"2\" label=\"Text\" >"._("Text")."</option></select></td>" );				
				echo ( "</tr>" );
				echo ( "<tr>" );
					echo ( "<td>"._("Hide Menu: ")."</td>" );
					echo ( "<td><input type=\"checkbox\" id=\"hidemenu\" name=\"hidemenu\" " . ( ( $GLOBALS['FOGCore']->getSetting( "FOG_PXE_MENU_HIDDEN" ) == "1" ) ? "checked=\"checked\"" : "" ) . " id=\"timeout\" /></td>" );				
				echo ( "</tr>" );
				echo ( "<tr>" );
					echo ( "<td>"._("Menu Timeout (in seconds): *")."</td>" );
					echo ( "<td><input type=\"text\" name=\"timeout\" value=\"" . $GLOBALS['FOGCore']->getSetting( "FOG_PXE_MENU_TIMEOUT" ) . "\" id=\"timeout\" /></td>" );				
				echo ( "</tr>" );			
				echo ( "<tr>" );
					echo ( "<td>"._("Master password: *")."</td>" );
					echo ( "<td><input type=\"password\" name=\"masterpassword\" value=\"\" id=\"masterpassword\" /></td>" );				
				echo ( "</tr>" );				
				echo ( "<tr>" );
					echo ( "<td>"._("Memory Test password (blank for none): ")."</td>" );
					echo ( "<td><input type=\"password\" name=\"memtestpassword\" value=\"\" id=\"memtestpassword\" /></td>" );				
				echo ( "</tr>" );			
				echo ( "<tr>" );
					echo ( "<td>"._("fog.reginput password (blank for none): ")."</td>" );
					echo ( "<td><input type=\"password\" name=\"reginputpassword\" value=\"\" id=\"reginputpassword\" /></td>" );				
				echo ( "</tr>" );			
				echo ( "<tr>" );
					echo ( "<td>"._("fog.reg password (blank for none): ")."</td>" );
					echo ( "<td><input type=\"password\" name=\"regpassword\" value=\"\" id=\"regpassword\" /></td>" );				
				echo ( "</tr>" );			
				echo ( "<tr>" );
					echo ( "<td>"._("fog.quickimage password (blank for none): ")."</td>" );
					echo ( "<td><input type=\"password\" name=\"quickimage\" value=\"\" id=\"quickimage\" /></td>" );				
				echo ( "</tr>" );			
				echo ( "<tr>" );
					echo ( "<td>"._("fog.sysinfo password (blank for none): ")."</td>" );
					echo ( "<td><input type=\"password\" name=\"sysinfo\" value=\"\" id=\"sysinfo\" /></td>" );				
				echo ( "</tr>" );			
				echo ( "<tr>" );
					echo ( "<td>"._("debug password (blank for none): ")."</td>" );
					echo ( "<td><input type=\"password\" name=\"debugpassword\" value=\"\" id=\"debugpassword\" /></td>" );				
				echo ( "</tr>" );						
				echo ( "<tr>" );
					echo ( "<td><a href=\"#\" onclick=\"$('#advancedTextArea').toggle(); return false;\" id=\"pxeAdvancedLink\"> "._("Advanced Configuration Options")."</a></td>" );
					echo ( "<td></td>" );				
				echo ( "</tr>" );
				echo ( "<tr>" );	
					echo ( "<td colspan=\"2\"><div id=\"advancedTextArea\" class=\"hidden\"><div class=\"lighterText tabbed\">"._("Add any custom text you would like included added as part of your <i>default</i> file.")." </div><textarea rows=\"5\" cols=\"64\" name=\"adv\">" . $GLOBALS['FOGCore']->getSetting( "FOG_PXE_ADVANCED" ) . "</textarea></div></td>" );			
				echo ( "</tr>" );
			echo ( "</table>" );
			
			echo ( "<p><input type=\"submit\" value=\""._("Save PXE Menu")."\" /></p>" );
		echo ( "</form>" );
	}
	
	// PXE Menu: POST
	public function pxemenu_post()
	{
		$conn = $this->DB->getLink();
		$reason;
		if ( generatePXEMenu( $conn, $_POST["menutype"], $_POST["masterpassword"], $_POST["memtestpassword"], $_POST["reginputpassword"], $_POST["regpassword"], $_POST["quickimage"], $_POST["sysinfo"], $_POST["debugpassword"], $_POST["timeout"], $_POST["hidemenu"] == "on", $_POST['adv'], $reason ) )
		{
			msgBox( _("PXE Menu has been updated!") );
		}
		else
		{
			msgBox( _("PXE Menu updated failed!")."<br />" . $reason);
		}
	}
	
	// Client Updater
	public function client_updater()
	{
		// Set title
		$this->title = _("FOG Client Service Updater");
		
		$conn = $this->DB->getLink();
		?>
		<div class="hostgroup">
			<?php
			echo _("This section allows you to update the modules and config files that run on the client computers.  The clients will checkin with the server from time to time to see if a new module is published.  If a new module is published the client will download the module and use it on the next time the service is started. ");
			?>
		</div>

		<table width="100%" cellpadding="0" cellspacing="0">
		<tr bgcolor="#BDBDBD"><td>&nbsp;Module Name</td><td>&nbsp;Module MD5</td><td>&nbsp;Module Type</td><td>&nbsp;Delete</td></tr>
		<?php
			$sql = "SELECT * FROM clientUpdates order by cuName";
			$res = mysql_query( $sql, $conn ) or die( mysql_error() );
			if ( mysql_num_rows( $res ) > 0 )
			{
				while( $ar = mysql_fetch_array( $res ) )
				{		
					echo ( "<tr><td>&nbsp;" . $ar["cuName"] . "</td><td>&nbsp;" . $ar["cuMD5"] . "</td><td>&nbsp;" .  $ar["cuType"]  . "</td><td>&nbsp;<a href=\"?node=$_GET[node]&sub=$_GET[sub]&del=$ar[cuID]\"><img src=\"./images/deleteSmall.png\" class=\"noBorder\" /></a></td></tr>" );
				}
			}
			else
			{
				echo ( "<tr><td colspan='4'>&nbsp;<center>"._("No modules found.")."</center></td></tr>" );
			}
		?>
		</table>

		<p class="titleBottomLeft"><?php echo _("Upload a new client module / configuration file"); ?></p>
		<form method="post" action="<?php echo( "?node=$_GET[node]&sub=$_GET[sub]");?>" enctype="multipart/form-data">
			<input type="file" name="module" value="" /> <span class="lightColor"> <?php echo _("Max Size: "). ini_get("post_max_size"); ?></span>
			<p><input type="submit" value="<?php echo _("Upload File"); ?>" /></p>
		</form>
		<?php
	}
	
	// Client Updater: POST
	public function client_updater_post()
	{
		$conn = $this->DB->getLink();
		if ( $_GET["del"] != null && is_numeric($_GET["del"]))
		{
			$del = mysql_real_escape_string( $_GET["del"] );
			$sql = "delete from clientUpdates where cuID = '$del'";
			if (! mysql_query( $sql, $conn ) )
				msgBox( mysql_error() );
			else
				lg( _("Client module update deleted: ") . $del );
			
		}

		if ( $_FILES["module"] != null  )
		{
			if ( file_exists( $_FILES['module']['tmp_name'] ) )
			{
				$strContents = file_get_contents( $_FILES['module']['tmp_name'] );
				$md5 = md5( $strContents );	
				$strContents = base64_encode( $strContents );
				if ( $strContents != null )
				{
					$modname = mysql_real_escape_string( basename($_FILES['module']['name']) );
					$type = "bin";
					if ( endsWith( $modname, ".ini" ) )
						$type = "txt";
					
					
					
					$sql = "SELECT 
							count(*) as cnt 
						FROM 
							clientUpdates 
						WHERE 
							cuName = '$modname'";
					$res = mysql_query( $sql, $conn ) or die( mysql_error() );
					
					if ( $ar = mysql_fetch_array( $res ) )
					{
						if ( $ar["cnt"] == 0 )
						{
							$sql = "INSERT INTO
									clientUpdates (cuName, cuMD5, cuType, cuFile)
									values( '$modname', '$md5', '$type', '$strContents')";
						}
						else
						{
							$sql = "UPDATE
									clientUpdates 
								SET
									cuMD5 = '$md5',
									cuType = '$type',
									cuFile = '$strContents'
								WHERE
									cuName = '$modname'";
						}
						
						if ( ! mysql_query( $sql, $conn ) )
						{
							msgBox( mysql_error() );
						}
						else
							lg( _("Client update module uploaded: ") . $modname );
					}
				}
			}
		}
	}
	
	// MAC Address List
	public function mac_list()
	{
		// Set title
		$this->title = _("MAC Address Manufacturer Listing");
		
		?>
		<div class="hostgroup">
			<?php
			echo(_("This section allows you to import known mac address makers into the FOG database for easier identification."));
			?>
		</div>

		<div>
			<p>
			<?php echo(_("Current Records: ").$FOGCore->getMACLookupCount()); ?></p>
			
			<p>
				<input type="button" id="delete" value="<?php echo(_("Delete Current Records")); ?>" onclick="clearMacs();" />  <input style='margin-left: 20px' type="button" id="update" value="<?php echo(_("Update Current Listing")); ?>" onclick="updateMacs();" />
			</p>
			<br /><br />	
			<p>
			<?php echo(_("MAC address listing source: ")); ?><a href="http://standards.ieee.org/regauth/oui/oui.txt">http://standards.ieee.org/regauth/oui/oui.txt</a>
			</p>
		</div>
		<?php
	}
	
	// MAC Address List: POST
	public function mac_list_post()
	{
		if ( $_GET["update"] == "1" )
		{
			$f = "./other/oui.txt";
			if ( file_exists($f) )
			{
				$handle = fopen($f, "r");
				$start = 18;
				$imported = 0;
				while (!feof($handle)) 
				{
					$line = trim(fgets($handle));
					if ( preg_match( "#^([0-9a-fA-F][0-9a-fA-F][:-]){2}([0-9a-fA-F][0-9a-fA-F]).*$#", $line ) )
					{
						
						$macprefix = substr( $line, 0, 8 );					
						$maker = substr( $line, $start, strlen( $line ) - $start );
						try
						{
							if ( strlen(trim( $macprefix ) ) == 8 && strlen($maker) > 0 )
							{
								if ( $FOGCore->addUpdateMACLookupTable( $macprefix, $maker ) )
									$imported++;
							}
						}
						catch ( Exception $e )
						{
							echo ( $e->getMessage() . "<br />" );
						}
						
					}
				}
				fclose($handle);
				
				msgBox( $imported . _(" mac addresses updated!") );
			}
			else
			{
				msgBox( _("Unable to locate file: $f") );
			}
		}
		else if ( $_GET["clear"] == "1" )
		{
			$FOGCore->clearMACLookupTable();
		}
	}
	
	// FOG System Settings
	public function settings()
	{
		// Set title
		$this->title = _("FOG System Settings");
		
		?>
		<p class="hostgroup"><?php print _("This section allows you to customize or alter the way in which FOG operates.  Please be very careful changing any of the following settings, as they can cause issues that are difficult to troubleshoot."); ?></p>
		<form method="post" action="?node=<?php print $_GET["node"]; ?>&sub=<?php print $_GET["sub"]; ?>">
			<input type="hidden" value="1" name="update" />
		<?php
		
		$conn = $this->DB->getLink();

		$cats = getSettingCats($conn);
		for ($i = 0; $i < count($cats); $i++)
		{
			echo ( "<h3>" . $cats[$i] . "</h3>" );
			echo ( "<table width=\"80%\" cellpadding=\"0\" cellspacing=\"0\">" );
			
				$sql = "SELECT * FROM globalSettings WHERE settingCategory = '" . mysql_real_escape_string( $cats[$i] ) . "' ORDER BY settingID";
				$res = mysql_query( $sql, $conn ) or die( mysql_error() );
				if ( mysql_num_rows( $res ) > 0 )
				{
					while( $ar = mysql_fetch_array( $res ) )
					{		
						echo ( "<tr><td width=\"270\">&nbsp;" . $ar["settingKey"] . "</td><td>&nbsp;" );

						if (count(explode( chr(10),  $ar["settingValue"]) ) <= 1 )
							echo ( "<input type=\"text\" name=\"" . $ar["settingID"] . "\" value=\"" . $ar["settingValue"] . "\" />" );
						else
							echo ( "<textarea rows=\"3\" cols=\"25\" name=\"" . $ar["settingID"] . "\">" . $ar["settingValue"] . "</textarea>" );
						echo ( "</td><td><span class=\"icon icon-help hand\" title=\"" . $ar["settingDesc"] . "\"></span></td></tr>" );
					}
				}

			echo ( "</table>" );
			
			echo ( "<p><input type=\"submit\" value=\""._("Save Changes")."\" /></p>" );
		}

		?>
		</form>
		<?php
	}
	
	// FOG System Settings: POST
	public function settings_post()
	{
		$conn = $this->DB->getLink();
		
		$sql = "SELECT
				settingID
			FROM
				globalSettings
			ORDER BY
				settingID";
		$res = mysql_query( $sql, $conn ) or criticalError( mysql_error(), _("FOG :: Database error!") );
		while( $ar = mysql_fetch_array($res) )
		{
			$key = $ar["settingID"];
			$value = mysql_real_escape_string($_POST[$key]);
			$sql = "UPDATE globalSettings SET settingValue = '$value' WHERE settingID = '$key'";
			if ( ! mysql_query( $sql, $conn ) )
			{
				criticalError( mysql_error(), _("FOG :: Database error!") );
			}
		}
	}
	
	// SSH Shell
	public function shell()
	{
		// Set title
		$this->title = _("FOG Server Shell Access");
		
		?>
		<applet class="shell" width="600" height="377" archive="./lib/ssh/libbrowser.jar,./lib/ssh/SSHTerm-1.0.0.jar,./lib/ssh/SSHVnc.jar,./lib/ssh/SecureTunneling.jar,./lib/ssh/ShiFT.jar,./lib/ssh/j2ssh-common-0.2.7.jar,./lib/ssh/j2ssh-core-0.2.7.jar,./lib/ssh/cog-jglobus.jar,./lib/ssh/commons-logging.jar,./lib/ssh/cryptix-asn1-signed.jar,./lib/ssh/cryptix-signed.jar,./lib/ssh/cryptix32-signed.jar,./lib/ssh/filedrop.jar,./lib/ssh/jce-jdk13-135.jar,./lib/ssh/log4j-1.2.6.jar,./lib/ssh/openssh-pk-1.1.0.jar,./lib/ssh/puretls-signed.jar,./lib/ssh/putty-pk-1.1.0.jar,./lib/ssh/jlirc-unix-soc.jar" code="com.sshtools.sshterm.SshTermApplet">
			<param name=sshterm.autoconnect.host value="<?php print $_SERVER["HTTP_HOST"]; ?>" />
			<param name=sshterm.autoconnect.port value="<?php print $GLOBALS['FOGCore']->getSetting( "FOG_SSH_PORT"); ?>" />
			<param name=sshterm.autoconnect.username value="<?php print $GLOBALS['FOGCore']->getSetting( "FOG_SSH_USERNAME"); ?>" />
			<param name=sshterm.ui.autoHide value="true" />
		</applet>
		<?php
	}
	
	// Log Viewer
	public function log()
	{
		// Set title
		$this->title = _("FOG Log Viewer");
		
		echo ( "<p>" );
			echo ( "<form method=\"post\" action=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "\">" );
			echo ( "<p>"._("File: ") );
				echo ( "<select name=\"logtype\"> " );
				foreach (array("Multicast", "Scheduler", "Replicator") as $value)
				{
				    $selected = ($value == $_POST['logtype']) ? "selected=\"selected\"" : "";
				    echo ( "<option $selected value=\"$value\">$value</option>" );
				}
				echo ( "</select>" );
				echo ( "&nbsp;&nbsp;"._("Number of lines: ") );
				echo ( "<select name=\"n\"> " );
				foreach (array("20", "50", "100", "200", "400", "500", "1000") as $value)
				{
				    $selected = ($value == $_POST['n']) ? "selected=\"selected\"" : "";
				    echo ( "<option $selected value=\"$value\">$value</option>" );
				}
				echo ( "</select>" );
				echo ( "&nbsp;&nbsp;<input type=\"submit\" value=\""._("Refresh")."\" />" );			
			echo ( "</p>" );
			echo ( "</form>" );
			echo ( "<div class=\"sub l\"><pre>" );

			
				$n = 20;
				if ( $_POST["n"] != null && is_numeric($_POST["n"]) )
					$n = $_POST["n"];
			
				$t = trim($_POST["logtype"]);
				$logfile = $GLOBALS['FOGCore']->getSetting( "FOG_UTIL_BASE" ) . "/log/multicast.log";
				if ( $t == "Multicast" )
					$logfile = $GLOBALS['FOGCore']->getSetting( "FOG_UTIL_BASE" ) . "/log/multicast.log";
				else if ( $t == "Scheduler" )
					$logfile = $GLOBALS['FOGCore']->getSetting( "FOG_UTIL_BASE" ) . "/log/fogscheduler.log";
				else if ( $t == "Replicator" )
					$logfile = $GLOBALS['FOGCore']->getSetting( "FOG_UTIL_BASE" ) . "/log/fogreplicator.log";				
			
				system("tail -n $n \"$logfile\"");
			echo ( "</pre></div>" );
		echo ( "</p>" );
	}
}

// Register page with FOGPageManager
$FOGPageManager->register(new FOGConfigurationPage());