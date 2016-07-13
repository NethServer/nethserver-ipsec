Summary: NethServer IPsec-based VPN configuration
Name: nethserver-ipsec
Version: 1.1.8
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

Requires: openswan, xl2tpd
Requires: nethserver-firewall-base, nethserver-vpn, nethserver-samba

BuildRequires: perl
BuildRequires: nethserver-devtools 

%description
Configures a VPN based on IPsec protocol

%prep
%setup

%build
%{makedocs}
perl createlinks
mkdir -p root%{perl_vendorlib}
mv -v lib/perl/NethServer root%{perl_vendorlib}

%install
rm -rf $RPM_BUILD_ROOT
(cd root; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} $RPM_BUILD_ROOT \
   --file /etc/ipsec.d/nsspassword 'attr(0600,root,root)' \
  > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)

%changelog
* Wed Jul 13 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.8-1
- ipsec tunnel: properly reject unencrypted traffic - Bug #3416 [NethServer]

* Mon Jun 06 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.7-1
- ipsec tunnel restart blocks udp traffic - Enhancement #3393 [NethServer]

* Thu Mar 03 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.6-1
- ipsec net2net tunnels error - Bug #3356

* Thu Feb 18 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.5-1
- IPsec tunnels with PPPoE - Enhancement #3339 [NethServer]

* Fri Dec 04 2015 Davide Principi <davide.principi@nethesis.it> - 1.1.4-1
- Relax validators of IPsec tunnel id fields  - Enhancement #3331 [NethServer]

* Mon Nov 30 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.3-1
- ipsec tunnels firewall to firewall - Enhancement #3326 [NethServer]
- L2TP not working with multiwan - Bug #3317 [NethServer]

* Tue Sep 29 2015 Davide Principi <davide.principi@nethesis.it> - 1.1.2-1
- Make Italian language pack optional - Enhancement #3265 [NethServer]

* Thu Aug 27 2015 Davide Principi <davide.principi@nethesis.it> - 1.1.1-1
- ipsec.log not rotated - Bug #3237 [NethServer]
- Firewall rules: support hosts within VPN zones - Enhancement #3233 [NethServer]

* Thu Jul 16 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.0-1
- IPsec tunnels (net2net) web interface - Feature #3194 [NethServer]
- Event trusted-networks-modify - Enhancement #3195 [NethServer]

* Wed Mar 11 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1
- VPN: missing firewall policy - Bug #3052 [NethServer]
- nethserver-devbox replacements - Feature #3009 [NethServer]

* Tue Dec 09 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1.ns6
- DNS: remove role property from dns db key - Enhancement #2915 [NethServer]
- Firewall: select default policy - Feature #2714 [NethServer]

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.0.1-1.ns6
- Move admin user in LDAP DB - Feature #2492 [NethServer]
- IPSec: honor VPNClientAccess property - Enhancement #2294 [NethServer]

* Tue Oct 22 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.0-1.ns6
- VPN: support IPsec/L2TP - Feature #1957 [NethServer]


