#!/usr/bin/ruby

def progressBar
  return Thread.new {
    Thread.stop
    loop do
      sleep(0.3)
      print '.'
    end

  }
end

today = Time.now.strftime('%Y-%m-%d')

if `svn info`.to_a[1] !~ /^URL: (.*)\/trunk$/
	puts "Repository invalid. Must be in trunk."
	exit
end

repo_url = $1
repo = repo_url.split('/').last
release_url = repo_url + "/releases"

pbar = progressBar
pbar.run

site_releases = `svn ls #{release_url}`.to_a
arr_release_numbers = Array.new
site_releases.each{|release|
    if release =~ /#{today}--(\d+)/
        arr_release_numbers << $1.to_i
    end
}

if arr_release_numbers.length > 0
   last_release_number = arr_release_numbers.sort.last + 1
else
    last_release_number = 1
end

Thread.kill(pbar)
print "\n"

release = "#{today}--#{last_release_number}"

if !ARGV[0].nil?
	msg = ARGV[0]
else
	print "Trac ticket #: "
	msg = $stdin.gets.chomp
end

cmd = "svn copy #{repo_url}/trunk #{release_url}/#{release} -m '#{msg}'"
puts cmd
puts

print "Execute command? "

if $stdin.gets.chomp.upcase != 'Y'
	puts "Nothing done."
	exit
end
	
`#{cmd}`
puts "Copied as #{release}."
puts "Execute on the remote machine:"
puts "svn switch #{release_url}/#{release}"
