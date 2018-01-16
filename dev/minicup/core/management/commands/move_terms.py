# coding=utf-8
from datetime import timedelta

from django.core.management.base import BaseCommand
from django.db.models import F

from core.models import MatchTerm


class Command(BaseCommand):
    def add_arguments(self, parser):
        parser.add_argument('year', type=int)

    def handle(self, *args, **options):
        year = options.get('year')

        terms = MatchTerm.objects.filter(day__year__slug=year)

        terms.update(
            start=F('start') - timedelta(minutes=30),
            end=F('end') - timedelta(minutes=30)
        )
