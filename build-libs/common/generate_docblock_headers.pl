#!/usr/bin/perl 

my $target_directory = $ARGV[0];
my $docblock_file    = $ARGV[1];
my $phal_version     = $ARGV[2];
my $docblock_template = undef;
my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
my $readable_year = sprintf "%04d",($year + 1900);

sub readDocBlockHeaderTemplate($) {
  $file = shift;

  local *FILE;
  open (FILE, "<$file") or die "$0: ERROR: Could not open $file for Reading";
  $docblock_content= join ('',<FILE>);
  close (FILE);
  return $docblock_content;
}

sub appendHeaderDocBlock($$) {

  $file = shift;
  $docblock_content = shift;

  $file =~ s/\\/\\\\/g;

  local *FILE;
  open (FILE, "<$file") or die "$0: ERROR: Could not open $file for Reading";
  $filecontents = join ('',<FILE>);
  close (FILE);


  if( $filecontents =~ s/^\s*\<\?php\s*?\n\s*?\n//g ) {
    #append the header:
    $filecontents = "<?php\n" . $docblock_content . "\n" . $filecontents;
    #pass to unix mode:
    $filecontents =~ s/\x0D\x0A/\x0A/g;

    if (!(-w $file)) {
      `chmod 755 $file`;
    }

    open(FILE, "> $file");
    binmode(FILE);
    print FILE $filecontents;
    close (FILE);
  }

}

sub generateHeaders($$$) {  

    local $path = shift;
    local $current_package = shift;
    local $current_subpackage = shift;
    local (@dir, $entry, $fullpath, $docblock);

    $new_package = getPackageFile($path);
    if($new_package) {
       $current_package = $new_package;
    }
    $new_subpackage = getSubPackageFile($path);
    if($new_subpackage) {
       $current_subpackage = $new_subpackage;
    }

    opendir (DIR, $path);
    @dir = readdir(DIR);  # get list of current directory
    closedir (DIR);
    foreach $entry (sort {lc($a) cmp lc($b)} @dir) {
        if ($entry eq '.' or $entry eq '..') { next; }
        $fullpath = "$path/$entry";
        if (-d $fullpath) {
            generateHeaders ($fullpath, $current_package, $current_subpackage);  # recursive directory listing
        } else {
            $docblock = $docblock_template;
            if($current_subpackage) {
                $docblock =~ s/\[subpackage\]/\@subpackage $current_subpackage/g;
            }
            else {
                $docblock =~ s/\[subpackage\]//g;
            }
            if($current_package) {
                $docblock =~ s/\[package\]/$current_package/g;
                $docblock =~ s/\[year\]/$readable_year/g;
                $docblock =~ s/\[version\]/$phal_version/g;
                appendHeaderDocBlock($fullpath, $docblock);
            }
        }
    }
}

sub getPackageFile($) {  

    local $path = shift;
    local (@dir, $entry, $fullpath);

    opendir (DIR, $path);
    @dir = readdir(DIR);  # get list of current directory
    closedir (DIR);
    foreach $entry (sort {lc($a) cmp lc($b)} @dir) {
        if ($entry eq '.' or $entry eq '..') { next; }
        $fullpath = "$path/$entry";
        if (!-d $fullpath) {
          if ($entry =~ /^(.+)\.pkg$/i) {
              return $1;
	    
          }
        }
    }
}

sub getSubPackageFile($) {  

    local $path = shift;
    local (@dir, $entry, $fullpath);

    opendir (DIR, $path);
    @dir = readdir(DIR);  # get list of current directory
    closedir (DIR);
    foreach $entry (sort {lc($a) cmp lc($b)} @dir) {
        if ($entry eq '.' or $entry eq '..') { next; }
        $fullpath = "$path/$entry";
        if (!-d $fullpath) {
          if ($entry =~ /^(.+)\.subpkg$/i) {
              return $1;
        
          }
        }
    }
}


$docblock_template = readDocBlockHeaderTemplate($docblock_file);
generateHeaders($target_directory, undef, undef);
