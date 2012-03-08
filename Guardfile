#!/usr/bin/env ruby
#^syntax detection

guard 'bundler' do
  watch('Gemfile')
end

guard 'phpunit', :tests_path => 'tests/koi', :cli => '--bootstrap ../unittest/bootstrap.php --colors' do
  watch(%r{^.+Test\.php$})

  watch(%r{^classes/(.+)\.php$}) do |m|
    path = m[1].gsub(/^kohana\//, '').gsub(/^koi\//, '')

    if path == "koi"
      "tests/koi/CoreTest.php"
    else
      "tests/koi/#{path.split('/').collect {|x| x.capitalize }.join('/')}Test.php"
    end
  end
end

guard 'phpcs', :standard => '../coding-standards/PHP/CodeSniffer/Standards/Kohana' do
  watch(%r{^.+\.php$})
end
