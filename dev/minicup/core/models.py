# coding=utf-8
# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey has `on_delete` set to the desired behavior.
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from __future__ import unicode_literals

from django.db import models
from django.utils.translation import ugettext as _


class Category(models.Model):
    year = models.ForeignKey('Year', models.DO_NOTHING, blank=True, null=True)
    name = models.CharField(max_length=30)
    slug = models.CharField(max_length=30)
    default = models.IntegerField()

    def __str__(self):
        return _('{}').format(self.name)

    class Meta:
        managed = False
        db_table = 'category'
        unique_together = (('year', 'slug'),)


class Day(models.Model):
    day = models.DateField()
    year = models.ForeignKey('Year', models.DO_NOTHING)

    def __str__(self):
        return _('{}. {}. {}').format(self.day.day, self.day.month, self.year)

    class Meta:
        managed = False
        db_table = 'day'


class DbMigrations(models.Model):
    version = models.CharField(primary_key=True, max_length=255)

    class Meta:
        managed = False
        db_table = 'db_migrations'


class Match(models.Model):
    match_term = models.ForeignKey('MatchTerm', models.CASCADE, blank=True, null=True)
    category = models.ForeignKey(Category, models.DO_NOTHING)
    home_team_info = models.ForeignKey('TeamInfo', models.CASCADE, related_name='match_home_team_info')
    away_team_info = models.ForeignKey('TeamInfo', models.CASCADE, related_name='match_away_team_info')
    score_home = models.IntegerField(blank=True, null=True)
    score_away = models.IntegerField(blank=True, null=True)
    confirmed = models.DateTimeField(blank=True, null=True)
    confirmed_as = models.IntegerField(blank=True, null=True)

    def __str__(self):
        return _('{} vs. {}').format(self.home_team_info, self.away_team_info)

    class Meta:
        managed = False
        db_table = 'match'
        unique_together = (('category', 'home_team_info', 'away_team_info'),)
        ordering = ('match_term__day__day', 'match_term__start')


class MatchTerm(models.Model):
    start = models.DateTimeField()
    end = models.DateTimeField()
    day = models.ForeignKey(Day, models.DO_NOTHING)
    location = models.CharField(max_length=50)

    def __str__(self):
        return _('{} | {}').format(self.start.time(), self.day)

    class Meta:
        managed = False
        db_table = 'match_term'
        ordering = ('day', 'start')


class News(models.Model):
    title = models.TextField()
    content = models.TextField()
    updated = models.DateTimeField()
    added = models.DateTimeField()
    year = models.ForeignKey('Year', models.DO_NOTHING, blank=True, null=True)
    texy = models.IntegerField()
    tag = models.ForeignKey('Tag', models.DO_NOTHING, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'news'


class OnlineReport(models.Model):
    match = models.ForeignKey(Match, models.DO_NOTHING)
    message = models.TextField()
    type = models.CharField(max_length=20)
    updated = models.DateTimeField()
    added = models.DateTimeField()
    author = models.ForeignKey('User', models.DO_NOTHING, db_column='author')

    class Meta:
        managed = False
        db_table = 'online_report'


class Photo(models.Model):
    filename = models.TextField()
    added = models.DateTimeField()
    taken = models.DateTimeField()
    active = models.IntegerField()
    year = models.ForeignKey('Year', models.DO_NOTHING, blank=True, null=True)
    author = models.TextField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'photo'


class PhotoTag(models.Model):
    photo = models.ForeignKey(Photo, models.DO_NOTHING, primary_key=True)
    tag = models.ForeignKey('Tag', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'photo_tag'
        unique_together = (('photo', 'tag'),)


class StaticContent(models.Model):
    slug = models.CharField(max_length=50)
    content = models.TextField()
    updated = models.DateTimeField(blank=True, null=True)
    year = models.ForeignKey('Year', models.DO_NOTHING, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'static_content'


class Tag(models.Model):
    name = models.CharField(max_length=50, blank=True, null=True)
    slug = models.CharField(max_length=50)
    is_main = models.IntegerField()
    main_photo = models.ForeignKey(Photo, models.DO_NOTHING, blank=True, null=True)
    year = models.ForeignKey('Year', models.DO_NOTHING, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'tag'
        unique_together = (('name', 'year'), ('slug', 'year'),)

    def __str__(self):
        return _('Tag {}:{}').format(self.year, self.name)


class Team(models.Model):
    category = models.ForeignKey(Category, models.DO_NOTHING)
    team_info = models.ForeignKey('TeamInfo', models.CASCADE)
    order = models.IntegerField()
    points = models.IntegerField()
    scored = models.IntegerField()
    received = models.IntegerField()
    inserted = models.DateTimeField()
    actual = models.IntegerField()
    after_match = models.ForeignKey(Match, models.DO_NOTHING, blank=True, null=True)

    def __str__(self):
        return _('Team record for {}').format(self.team_info)

    class Meta:
        managed = False
        db_table = 'team'


class TeamInfo(models.Model):
    category = models.ForeignKey(Category, models.DO_NOTHING)
    name = models.CharField(max_length=30)
    slug = models.CharField(max_length=30)
    static_content = models.ForeignKey(StaticContent, models.DO_NOTHING, blank=True, null=True)
    tag = models.ForeignKey(Tag, models.DO_NOTHING, blank=True, null=True)

    def __str__(self):
        return self.name

    class Meta:
        managed = False
        db_table = 'team_info'
        unique_together = (('category', 'slug'), ('category', 'name'),)


class User(models.Model):
    username = models.TextField()
    password_hash = models.TextField()
    fullname = models.TextField()

    class Meta:
        managed = False
        db_table = 'user'


class Year(models.Model):
    year = models.TextField()  # This field type is a guess.
    name = models.TextField(blank=True, null=True)
    slug = models.CharField(unique=True, max_length=20)
    actual = models.IntegerField()

    def __str__(self):
        return _('{}').format(self.name or self.slug)

    class Meta:
        managed = False
        db_table = 'year'
