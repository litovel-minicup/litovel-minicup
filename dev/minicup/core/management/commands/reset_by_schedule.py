# coding=utf-8
import json
from pprint import pprint

from django.core.management.base import BaseCommand

from core.models import MatchTerm, TeamInfo, Match, Category


class Command(BaseCommand):
    def add_arguments(self, parser):
        parser.add_argument('category', type=str)
        parser.add_argument('file', type=str)

    def handle(self, *args, **options):
        schedule = open(options.get('file'))

        data = json.load(schedule)

        category_slug = options.get('category')

        category = Category.objects.filter(slug=category_slug, year__year=2017).first()
        terms = list(MatchTerm.objects.filter(day__year__year=2017).order_by('day__day', 'start'))
        teams = list(TeamInfo.objects.filter(category__slug=category_slug, category__year__year=2017))
        db_matches = Match.objects.filter(category__slug=category_slug, category__year__year=2017)
        db_matches.delete()

        for i, term in enumerate(terms):
            if (i % 3) in (2,):
                pass  # term.delete()

        # return
        pprint(teams)
        match_id = 0
        for term in data.get('periods'):
            for matches in term:
                for match in matches.get('matches'):
                    home, away = teams[int(match.get('teams')[0]) - 1], teams[int(match.get('teams')[1]) - 1]

                    pprint((terms[match_id].pk, terms[match_id]))
                    match_to_change = Match()

                    match_to_change.home_team_info = home
                    match_to_change.away_team_info = away
                    match_to_change.category = category
                    match_to_change.match_term = terms[match_id]

                    match_to_change.save()

                    match_id += 1
